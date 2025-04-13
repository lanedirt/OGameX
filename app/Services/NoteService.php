<?php

namespace OGame\Services;

use OGame\Models\Note;

/**
 * Class NoteService.
 *
 * NoteService object.
 *
 * @package OGame\Services
 */
class NoteService
{
    /**
     * The PlayerService object.
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * NoteService constructor.
     */
    public function __construct(PlayerService $player)
    {
        $this->player = $player;
    }

    /**
     * Create a new note for a player.
     *
     * @param array<string, mixed> $data
     * @return Note
     */
    public function createNoteForUser(array $data): Note
    {
        $note = new Note();
        $note->user_id = $this->player->getId();
        $note->priority = $data['priority'];
        $note->subject = $data['subject'];
        $note->content = $data['content'];
        $note->save();

        return $note;
    }

    /**
     * Retrieve all notes for the current player.
     *
     * @return Note[] Array of Note objects.
     */
    public function getAllNotesForUser(): array
    {
        return Note::where('user_id', $this->player->getId())
               ->orderBy('created_at', 'desc')
               ->get()
               ->all();
    }

    /**
     * Retrieve a specific note for the current user.
     *
     * @param int $noteId
     * @return Note|null
     */
    public function getNoteById(int $noteId): Note|null
    {
        return Note::where('id', $noteId)
            ->where('user_id', $this->player->getId())
            ->first();
    }

    /**
     * Update a specific note for the current user.
     *
     * @param int $noteId
     * @param array<string, mixed> $data
     * @return Note
     */
    public function updateNoteForUser(int $noteId, array $data): Note
    {
        $note = Note::where('id', $noteId)
            ->where('user_id', $this->player->getId())
            ->first();

        $note->update($data);

        return $note;
    }

    /**
     * Deletes all the notes.
     *
     * @return void
     */
    public function deleteAllNotesForUser(): void
    {
        Note::where('user_id', $this->player->getId())
            ->delete();
    }

    /**
     * Deletes marked notes.
     *
     * @param int[] $noteIds
     * @return void
     */
    public function deleteMarkedNotes(array $noteIds): void
    {
        Note::whereIn('id', $noteIds)
            ->where('user_id', $this->player->getId())
            ->delete();
    }

    /**
     * Check if a note exists and belongs to the current user.
     *
     * @param int $noteId
     * @return bool
     */
    public function noteExistsAndBelongsToUser(int $noteId): bool
    {
        return Note::where('id', $noteId)
            ->where('user_id', $this->player->getId())
            ->exists();
    }
}
