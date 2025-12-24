<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\AllianceService;
use OGame\Services\PlayerService;

class AllianceController extends OGameController
{
    /**
     * Shows the alliance index page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return View
     */
    public function index(AllianceService $allianceService, PlayerService $player): View
    {
        $this->setBodyId('alliance');

        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        $alliance = null;
        $member = null;
        $members = collect();
        $ranks = collect();
        $applications = collect();

        // If user is in an alliance, load alliance data
        if ($userAllianceId) {
            $alliance = $allianceService->getAllianceById($userAllianceId);
            $member = $allianceService->getAllianceMember($userAllianceId, $userId);
            $members = $allianceService->getAllianceMembers($userAllianceId);
            $ranks = $alliance !== null ? $alliance->ranks : collect();

            // Only load applications if user has permission
            if ($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEE_APPLICATIONS)) {
                $applications = $allianceService->getPendingApplications($userAllianceId);
            }
        }

        return view('ingame.alliance.index')->with([
            'alliance' => $alliance,
            'member' => $member,
            'members' => $members,
            'ranks' => $ranks,
            'applications' => $applications,
        ]);
    }

    /**
     * Shows the alliance creation page
     *
     * @return View
     */
    public function create(): View
    {
        return view('ingame.alliance.create');
    }

    /**
     * AJAX endpoint for alliance creation form
     *
     * @return JsonResponse
     */
    public function ajaxCreate(): JsonResponse
    {
        return response()->json([
            'content' => [
              'alliance/alliance_create' => view('ingame.alliance.create')->render(),
            ],
            'files' => [
              'js' => [],
              'css' => [],
            ],
            'newAjaxToken' => csrf_token(),
            'page' => [
              'stateObj' => [],
              'title' => 'OGameX',
              'url' => route('alliance.index'),
            ],
            'serverTime' => time(),
            'target' => 'alliance/alliance_create',
        ]);
    }

    /**
     * Create a new alliance
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'tag' => 'required|string|min:3|max:8',
            'name' => 'required|string|min:3|max:30',
        ]);

        try {
            $allianceService->createAlliance(
                $player->getId(),
                $validated['tag'],
                $validated['name']
            );

            $message = __('Alliance created successfully');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Apply to join an alliance
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse|RedirectResponse
     */
    public function apply(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'alliance_id' => 'required|integer|exists:alliances,id',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $allianceService->applyToAlliance(
                $player->getId(),
                $validated['alliance_id'],
                $validated['message'] ?? null
            );

            $message = __('Application submitted successfully');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle alliance actions (accept/reject applications, kick members, etc.)
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse|RedirectResponse
     */
    public function action(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse|RedirectResponse
    {
        $action = $request->input('action');
        $userId = $player->getId();

        try {
            $message = '';

            switch ($action) {
                case 'accept_application':
                    $applicationId = $request->input('application_id');
                    $allianceService->acceptApplication($applicationId, $userId);
                    $message = __('Application accepted');
                    break;

                case 'reject_application':
                    $applicationId = $request->input('application_id');
                    $allianceService->rejectApplication($applicationId, $userId);
                    $message = __('Application rejected');
                    break;

                case 'kick_member':
                    $memberUserId = $request->input('member_user_id');
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->kickMember($allianceId, $memberUserId, $userId);
                    $message = __('Member kicked from alliance');
                    break;

                case 'leave_alliance':
                    $allianceService->leaveAlliance($userId);
                    $message = __('You have left the alliance');
                    break;

                case 'assign_rank':
                    $memberUserId = $request->input('member_user_id');
                    $rankId = $request->input('rank_id');
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->assignRank($allianceId, $memberUserId, $rankId, $userId);
                    $message = __('Rank assigned');
                    break;

                case 'update_texts':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->updateTexts(
                        $allianceId,
                        $userId,
                        $request->input('internal_text'),
                        $request->input('external_text'),
                        $request->input('application_text')
                    );
                    $message = __('Alliance texts updated');
                    break;

                case 'disband_alliance':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->disbandAlliance($allianceId, $userId);
                    $message = __('Alliance disbanded');
                    break;

                default:
                    throw new Exception('Invalid action');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a new rank
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse|RedirectResponse
     */
    public function createRank(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'rank_name' => 'required|string|max:30',
            'permissions' => 'nullable|array',
        ]);

        try {
            $allianceId = auth()->user()->alliance_id;
            $allianceService->createRank(
                $allianceId,
                $validated['rank_name'],
                $validated['permissions'] ?? [],
                $player->getId()
            );

            $message = __('Rank created successfully');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
