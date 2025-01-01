<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use OGame\Services\NoteService;

class NotesController extends OGameController
{
    /**
     * Shows the notes popup page
     *
     * @param Request $request
     * @param NoteService $noteService
     * @return View
     */
    public function overlay(Request $request, NoteService $noteService): View
    {
        $data = [];

        $notesDeleted = false;

        if ($request->isMethod('post')) {
            $deleteMethod = $request->input('noticeDeleteMethode');

            if ($deleteMethod === "1") {
                $noteIds = $request->input('delIds', []);
                if (!empty($noteIds)) {
                    $noteService->deleteMarkedNotes($noteIds);
                    $notesDeleted = true;
                }
            } elseif ($deleteMethod === "2") {
                $noteService->deleteAllNotesForUser();
                $notesDeleted = true;
            }

            if ($notesDeleted) {
                $data['success'] = __('Note(s) has(ve) been deleted');
            }
        }

        $data['notes'] = $noteService->getAllNotesForUser();
        return view('ingame.notes.overlay', $data);
    }

    /**
     * Shows the notes view popup page
     *
     * @param Request $request
     * @param NoteService $noteService
     * @return View
     */
    public function view(Request $request, NoteService $noteService): View
    {
        $note = null;

        $noteId = $request->input('id');
        if ($noteId) {
            $note = $noteService->getNoteById($noteId);
        }

        return view('ingame.notes.create')->with([
            'noteId' => $note ? $note->id : 0,
            'priority' => $note ? $note->priority : 2,
            'subject' => $note ? $note->subject : '',
            'content' => $note ? $note->content : '',
        ]);
    }

    /**
     * Create a new note
     *
     * @param Request $request
     * @param NoteService $noteService
     * @return JsonResponse
     */
    public function ajaxCreate(Request $request, NoteService $noteService): JsonResponse
    {
        $validated = $request->validate([
            'id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($noteService) {
                    if (!empty($value) && !$noteService->noteExistsAndBelongsToUser($value)) {
                        $fail('The selected note ID does not exist.');
                    }
                },
            ],
            'noticePrio' => 'nullable|integer|min:1|max:3',
            'noticeSubject' => 'nullable|string|max:32',
            'noticeText' => 'nullable|string|max:5000',
        ]);

        $data = [
            'priority' => $validated['noticePrio'] ?? 2,
            'subject' => $validated['noticeSubject'] ?? __('Your subject'),
            'content' => $validated['noticeText'],
        ];

        try {
            if (!empty($validated['id'])) {
                // Update existing note
                $note = $noteService->updateNoteForUser($validated['id'], $data);
                $message = __('Note edited');
            } else {
                // Create new note
                $note = $noteService->createNoteForUser($data);
                $message = __('Note has been added');
            }

            return response()->json([
                'id' => $note->id,
                'error' => null,
                'success' =>  $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'id' => null,
                'error' => __('Failed to process note'),
                'success' => null
            ], 500);
        }
    }
}
