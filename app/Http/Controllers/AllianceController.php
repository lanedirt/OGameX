<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\AllianceService;
use OGame\Services\HighscoreService;
use OGame\Services\PlayerService;

class AllianceController extends OGameController
{
    /**
     * Shows the alliance index page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @param Request $request
     * @return View
     */
    public function index(AllianceService $allianceService, PlayerService $player, Request $request): View
    {
        $this->setBodyId('alliance');

        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        $alliance = null;
        $member = null;
        $members = collect();
        $ranks = collect();
        $applications = collect();
        $targetAllianceId = null;

        // Check if applying to a specific alliance
        if ($request->has('alliance_id') && !$userAllianceId) {
            $targetAllianceId = (int)$request->get('alliance_id');
        }

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
            'targetAllianceId' => $targetAllianceId,
        ]);
    }

    /**
     * Shows the application form for a specific alliance
     *
     * @param int $alliance_id
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return View
     */
    public function showApplicationForm(int $alliance_id, AllianceService $allianceService, PlayerService $player): View
    {
        $this->setBodyId('alliance');

        // User must not already be in an alliance
        if ($player->getUser()->alliance_id) {
            return redirect()->route('alliance.index')->with('error', __('You are already in an alliance'));
        }

        // Get the alliance
        $alliance = $allianceService->getAllianceById($alliance_id);
        if (!$alliance) {
            return redirect()->route('alliance.index')->with('error', __('Alliance not found'));
        }

        // Check if alliance is open for applications
        if (!$alliance->is_open) {
            return redirect()->route('alliance.index')->with('error', __('This alliance is closed for applications'));
        }

        return view('ingame.alliance.apply')->with([
            'alliance' => $alliance,
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
            'urlCreateAlliance' => route('alliance.store'),
        ]);
    }

    /**
     * AJAX endpoint for alliance overview page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxOverview(AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        if (!$userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are not in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($userAllianceId);
        $member = $allianceService->getAllianceMember($userAllianceId, $userId);
        $members = $allianceService->getAllianceMembers($userAllianceId);
        $ranks = $alliance !== null ? $alliance->ranks : collect();

        return response()->json([
            'content' => [
                'alliance/alliance_overview' => view('ingame.alliance.overview')->with([
                    'alliance' => $alliance,
                    'member' => $member,
                    'members' => $members,
                    'ranks' => $ranks,
                ])->render(),
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
            'target' => 'alliance/alliance_overview',
            'urlKickMember' => route('alliance.members.kick'),
            'urlSubmitRanks' => route('alliance.members.assign-rank'),
            'urlLeaveAlliance' => route('alliance.action'),
        ]);
    }

    /**
     * AJAX endpoint for alliance management page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxManagement(AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        if (!$userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are not in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($userAllianceId);
        $member = $allianceService->getAllianceMember($userAllianceId, $userId);
        $ranks = $alliance !== null ? $alliance->ranks : collect();

        return response()->json([
            'content' => [
                'alliance/alliance_management' => view('ingame.alliance.management')->with([
                    'alliance' => $alliance,
                    'member' => $member,
                    'ranks' => $ranks,
                ])->render(),
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
            'target' => 'alliance/alliance_management',
            'urlCreateRank' => route('alliance.rank.create'),
            'urlUpdateRank' => route('alliance.action') . '?action=update_rank',
            'urlDeleteRank' => route('alliance.action') . '?action=delete_rank',
            'urlUpdateAllianceText' => route('alliance.text.update'),
            'urlUpdateSettings' => route('alliance.action') . '?action=update_settings',
            'urlUpdateTag' => route('alliance.action') . '?action=update_tag',
            'urlUpdateName' => route('alliance.action') . '?action=update_name',
            'urlUpdateTagName' => route('alliance.action') . '?action=update_tag_name',
            'urlDissolve' => route('alliance.action') . '?action=disband_alliance',
        ]);
    }

    /**
     * AJAX endpoint for alliance broadcast page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxBroadcast(AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        try {
            $userId = $player->getId();
            $userAllianceId = auth()->user()->alliance_id;

            if (!$userAllianceId) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'You are not in an alliance',
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            $alliance = $allianceService->getAllianceById($userAllianceId);
            $member = $allianceService->getAllianceMember($userAllianceId, $userId);
            $ranks = $alliance !== null ? $alliance->ranks : collect();

            return response()->json([
                'content' => [
                    'alliance/alliance_broadcast' => view('ingame.alliance.broadcast')->with([
                        'alliance' => $alliance,
                        'member' => $member,
                        'ranks' => $ranks,
                    ])->render(),
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
                'target' => 'alliance/alliance_broadcast',
                'urlSend' => route('alliance.action') . '?action=send_broadcast&asJson=1',
            ]);
        } catch (\Exception $e) {
            \Log::error('Alliance broadcast error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'failure',
                'message' => 'Error loading broadcast: ' . $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ], 500);
        }
    }

    /**
     * AJAX endpoint for alliance applications page
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxApplications(AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        if (!$userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are not in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($userAllianceId);
        $member = $allianceService->getAllianceMember($userAllianceId, $userId);
        $applications = collect();

        // Only load applications if user has permission
        if ($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEE_APPLICATIONS)) {
            $applications = $allianceService->getPendingApplications($userAllianceId);
        }

        return response()->json([
            'content' => [
                'alliance/alliance_applications' => view('ingame.alliance.applications')->with([
                    'alliance' => $alliance,
                    'member' => $member,
                    'applications' => $applications,
                ])->render(),
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
            'target' => 'alliance/alliance_applications',
            'urlAccept' => route('alliance.action'),
            'urlDeny' => route('alliance.action'),
            'urlReport' => route('alliance.action'),
        ]);
    }

    /**
     * AJAX endpoint to get the alliance classes tab
     *
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxClasses(AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $userId = $player->getId();
        $userAllianceId = auth()->user()->alliance_id;

        if (!$userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are not in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($userAllianceId);
        $member = $allianceService->getAllianceMember($userAllianceId, $userId);

        return response()->json([
            'content' => [
                'alliance/alliance_classes' => view('ingame.alliance.classes')->with([
                    'alliance' => $alliance,
                    'member' => $member,
                ])->render(),
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
            'target' => 'alliance/alliance_classes',
        ]);
    }

    /**
     * AJAX endpoint to get the application form for a specific alliance
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxNewApplication(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $allianceId = $request->input('appliedAllyId');
        $userAllianceId = auth()->user()->alliance_id;

        // User must not already be in an alliance
        if ($userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are already in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        // Validate alliance exists
        if (!$allianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Alliance ID is required',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($allianceId);
        if (!$alliance) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Alliance not found',
                'newAjaxToken' => csrf_token(),
            ], 404);
        }

        // Check if alliance is open for applications
        if (!$alliance->is_open) {
            return response()->json([
                'status' => 'failure',
                'message' => 'This alliance is closed for applications',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        return response()->json([
            'content' => [
                'alliance/alliance_handleApplication' => view('ingame.alliance.handle_application')->with([
                    'alliance' => $alliance,
                    'allianceId' => $allianceId,
                ])->render(),
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
            'target' => 'alliance/alliance_handleApplication',
            'urlSendApplication' => route('alliance.apply'),
        ]);
    }

    /**
     * AJAX endpoint for handling alliance application (from galaxy view)
     *
     * @param int $alliance_id
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxHandleApplication(int $alliance_id, AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $userAllianceId = auth()->user()->alliance_id;

        // User must not already be in an alliance
        if ($userAllianceId) {
            return response()->json([
                'status' => 'failure',
                'message' => 'You are already in an alliance',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        $alliance = $allianceService->getAllianceById($alliance_id);
        if (!$alliance) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Alliance not found',
                'newAjaxToken' => csrf_token(),
            ], 404);
        }

        // Check if alliance is open for applications
        if (!$alliance->is_open) {
            return response()->json([
                'status' => 'failure',
                'message' => 'This alliance is closed for applications',
                'newAjaxToken' => csrf_token(),
            ], 400);
        }

        return response()->json([
            'content' => [
                'alliance/alliance_handleApplication' => view('ingame.alliance.handle_application')->with([
                    'alliance' => $alliance,
                    'allianceId' => $alliance_id,
                ])->render(),
            ],
            'files' => [
                'js' => [],
                'css' => [],
            ],
            'newAjaxToken' => csrf_token(),
            'page' => [
                'stateObj' => [],
                'title' => 'OGameX',
                'url' => route('alliance.index', ['alliance_id' => $alliance_id]),
            ],
            'serverTime' => time(),
            'target' => 'alliance/alliance_handleApplication',
            'urlSendApplication' => route('alliance.apply'),
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
                    'status' => 'success',
                    'message' => $message,
                    'redirectUrl' => route('alliance.index'),
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'failure',
                    'message' => $e->getMessage(),
                    'errors' => [['message' => $e->getMessage()]],
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
            'message' => 'nullable|string|max:2000',
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

        \Log::info('Alliance action called', [
            'action' => $action,
            'all_input' => $request->all(),
            'query' => $request->query->all(),
        ]);

        try {
            $message = '';
            $redirectUrl = null;

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

                case 'update_rank':
                    $allianceId = auth()->user()->alliance_id;
                    // Extract rank permissions from request
                    // JavaScript sends params like: rankId_36: 123, rankId_37: 456
                    $rankPermissions = [];
                    foreach ($request->all() as $key => $value) {
                        if (str_starts_with($key, 'rankId_')) {
                            $rankId = (int)str_replace('rankId_', '', $key);
                            $rankPermissions[$rankId] = (int)$value;
                        }
                    }
                    $allianceService->updateRankPermissions($allianceId, $rankPermissions, $userId);
                    $message = __('Rank permissions updated');
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

                case 'update_settings':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->updateSettings(
                        $allianceId,
                        $userId,
                        [
                            'homepage' => $request->input('homepageUrl'),
                            'logo_url' => $request->input('logoUrl'),
                            'open' => $request->input('state'),
                            'founder_rank_name' => $request->input('foundername'),
                            'newcomer_rank_name' => $request->input('newcomerrankname'),
                            'language' => $request->input('language'),
                        ]
                    );
                    $message = __('Alliance settings updated');
                    break;

                case 'update_tag':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->updateTag(
                        $allianceId,
                        $userId,
                        $request->input('newTag')
                    );
                    $message = __('Alliance tag updated');
                    break;

                case 'update_name':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->updateName(
                        $allianceId,
                        $userId,
                        $request->input('newName')
                    );
                    $message = __('Alliance name updated');
                    break;

                case 'update_tag_name':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->updateTag(
                        $allianceId,
                        $userId,
                        $request->input('newTag')
                    );
                    $allianceService->updateName(
                        $allianceId,
                        $userId,
                        $request->input('newName')
                    );
                    $message = __('Alliance tag and name updated');
                    break;

                case 'disband_alliance':
                    $allianceId = auth()->user()->alliance_id;
                    $allianceService->disbandAlliance($allianceId, $userId);
                    $message = __('Alliance disbanded');
                    $redirectUrl = route('alliance.index');
                    break;

                case 'send_broadcast':
                    \Log::info('send_broadcast case reached', [
                        'user_id' => $userId,
                        'request_id' => $request->header('X-Request-ID', uniqid()),
                    ]);

                    $allianceId = auth()->user()->alliance_id;

                    // Support both old (rankIds/broadcastText) and new (recipients/text) parameter names
                    $text = $request->input('text') ?: $request->input('broadcastText');
                    $recipients = $request->input('recipients') ?: $request->input('rankIds', []);

                    // Ensure recipients is always an array
                    if (!is_array($recipients)) {
                        $recipients = [];
                    }

                    \Log::info('About to call sendBroadcastMessage', [
                        'alliance_id' => $allianceId,
                        'text_length' => strlen($text),
                    ]);

                    $allianceService->sendBroadcastMessage(
                        $allianceId,
                        $userId,
                        $text,
                        $recipients
                    );

                    \Log::info('sendBroadcastMessage completed');

                    $message = __('Broadcast message sent successfully');
                    break;

                default:
                    throw new Exception('Invalid action');
            }

            if ($request->expectsJson()) {
                $response = [
                    'status' => 'success',
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ];

                if ($redirectUrl) {
                    $response['redirectUrl'] = $redirectUrl;
                }

                return response()->json($response);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'failure',
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
            'rankName' => 'required|string|max:30',
            'permissions' => 'nullable|array',
        ]);

        try {
            $allianceId = auth()->user()->alliance_id;
            $allianceService->createRank(
                $allianceId,
                $validated['rankName'],
                $validated['permissions'] ?? [],
                $player->getId()
            );

            $message = __('Rank created successfully');

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            return redirect()->route('alliance.index')->with('success', $message);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'failure',
                    'message' => $e->getMessage(),
                    'newAjaxToken' => csrf_token(),
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Kick a member from the alliance
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function kickMemberAction(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $userId = $player->getId();
            $userAllianceId = auth()->user()->alliance_id;

            if (!$userAllianceId) {
                throw new Exception('You are not in an alliance');
            }

            $allianceService->kickMember($userAllianceId, $validated['user_id'], $userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Member kicked successfully',
                'newAjaxToken' => csrf_token(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failure',
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ], 400);
        }
    }

    /**
     * Assign a rank to an alliance member
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function assignRankAction(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'rank_id' => 'nullable|integer|exists:alliance_ranks,id',
        ]);

        try {
            $userId = $player->getId();
            $userAllianceId = auth()->user()->alliance_id;

            if (!$userAllianceId) {
                throw new Exception('You are not in an alliance');
            }

            $allianceService->assignRank(
                $userAllianceId,
                $validated['user_id'],
                $validated['rank_id'],
                $userId
            );

            $alliance = $allianceService->getAllianceById($userAllianceId);
            $rankName = $validated['rank_id']
                ? $alliance->ranks->firstWhere('id', $validated['rank_id'])->name
                : $alliance->newcomer_rank_name;

            return response()->json([
                'status' => 'success',
                'message' => "Rank assigned successfully to {$rankName}",
                'newAjaxToken' => csrf_token(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failure',
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ], 400);
        }
    }

    /**
     * Update alliance text (internal, external, or application text)
     *
     * @param Request $request
     * @param AllianceService $allianceService
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function updateAllianceText(Request $request, AllianceService $allianceService, PlayerService $player): JsonResponse
    {
        try {
            $userId = $player->getId();
            $userAllianceId = auth()->user()->alliance_id;

            if (!$userAllianceId) {
                throw new Exception('You are not in an alliance');
            }

            $validated = $request->validate([
                'submitType' => 'required|in:intern,extern,candidacy',
                'allianceText' => 'nullable|string|max:50000',
            ]);

            $submitType = $validated['submitType'];
            $allianceText = $validated['allianceText'] ?? '';

            // Get current alliance to preserve other texts
            $alliance = $allianceService->getAllianceById($userAllianceId);

            // Update only the specified text type
            $internalText = $alliance->internal_text;
            $externalText = $alliance->external_text;
            $applicationText = $alliance->application_text;

            switch ($submitType) {
                case 'intern':
                    $internalText = $allianceText;
                    break;
                case 'extern':
                    $externalText = $allianceText;
                    break;
                case 'candidacy':
                    $applicationText = $allianceText;
                    break;
            }

            $allianceService->updateTexts(
                $userAllianceId,
                $userId,
                $internalText,
                $externalText,
                $applicationText
            );

            return response()->json([
                'status' => 'success',
                'message' => __('Alliance text updated'),
                'newAjaxToken' => csrf_token(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failure',
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ], 400);
        }
    }

    /**
     * Show alliance information page (opens in new window)
     *
     * @param int $alliance_id
     * @param AllianceService $allianceService
     * @param HighscoreService $highscoreService
     * @return View
     */
    public function info(int $alliance_id, AllianceService $allianceService, HighscoreService $highscoreService, PlayerService $player): View
    {
        $alliance = $allianceService->getAllianceById($alliance_id);

        if (!$alliance) {
            abort(404, 'Alliance not found');
        }

        // Get member count
        $memberCount = $allianceService->getAllianceMembers($alliance_id)->count();

        // Get alliance rank in highscore (default to overall points - type 0)
        $highscoreService->setHighscoreType(0);
        $allianceRank = $highscoreService->getHighscoreAllianceRank($alliance_id);

        // Check if user can apply
        $canApply = $alliance->is_open && !$player->getUser()->alliance_id;

        return view('ingame.alliance.info')->with([
            'alliance' => $alliance,
            'memberCount' => $memberCount,
            'allianceRank' => $allianceRank > 0 ? $allianceRank : null,
            'canApply' => $canApply,
        ]);
    }
}
