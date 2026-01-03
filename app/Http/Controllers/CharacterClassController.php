<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use OGame\Enums\CharacterClass;
use OGame\Services\CharacterClassService;

class CharacterClassController extends OGameController
{
    /**
     * CharacterClassController constructor.
     *
     * @param CharacterClassService $characterClassService
     */
    public function __construct(
        private CharacterClassService $characterClassService
    ) {
    }

    /**
     * Shows the character class selection page
     *
     * @return View
     */
    public function index(): View
    {
        $this->setBodyId('characterclassselection');

        $user = Auth::user();
        $currentClass = $this->characterClassService->getCharacterClass($user);
        $changeCost = $this->characterClassService->getChangeCost($user);

        // Get all character classes
        $classes = [
            CharacterClass::COLLECTOR,
            CharacterClass::GENERAL,
            CharacterClass::DISCOVERER,
        ];

        return view('ingame.characterclass.index', [
            'currentClass' => $currentClass,
            'changeCost' => $changeCost,
            'classes' => $classes,
            'darkMatter' => $user->dark_matter,
            'isFreeSelection' => !$user->character_class_free_used,
        ]);
    }

    /**
     * Select a character class
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function selectClass(Request $request): JsonResponse
    {
        $request->validate([
            'characterClassId' => 'required|integer|in:1,2,3',
        ]);

        $user = Auth::user();
        $classId = (int)$request->input('characterClassId');
        $newClass = CharacterClass::from($classId);

        try {
            // Check if user can afford the change
            if (!$this->characterClassService->canChangeClass($user, $newClass)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough Dark Matter to change class',
                    'lackingDM' => true,
                ], 400);
            }

            // Select the class
            $this->characterClassService->selectClass($user, $newClass);

            // Refresh user to ensure changes are reflected
            $user->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Character class selected successfully',
                'newClass' => $newClass->getName(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Deselect current character class
     *
     * @return JsonResponse
     */
    public function deselectClass(): JsonResponse
    {
        $user = Auth::user();

        try {
            $this->characterClassService->deselectClass($user);

            // Refresh user to ensure changes are reflected
            $user->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Character class deactivated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
