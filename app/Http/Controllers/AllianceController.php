<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Services\AllianceMemberService;
use OGame\Services\AllianceService;
use OGame\Services\PlayerService;

class AllianceController extends OGameController
{
    /**
     * Shows the alliance index page
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        $this->setBodyId('alliance');

        // Get user's alliance if they have one
        $userAlliance = AllianceMemberService::getUserAlliance($player->getUser());
        $allianceService = null;
        $membership = null;
        $userRank = null;
        $members = collect();
        $pendingApplications = collect();

        if ($userAlliance) {
            $allianceService = resolve(AllianceService::class, ['alliance_id' => $userAlliance->id]);
            $membership = $userAlliance->members()->where('user_id', $player->getId())->first();
            $userRank = $membership?->rank;
            $members = $allianceService->getMembers()->load(['user', 'rank']);

            // Only show applications if user has permission
            if ($allianceService->hasPermission($player->getId(), 'can_see_applications')) {
                $pendingApplications = $allianceService->getPendingApplications();
            }
        }

        return view('ingame.alliance.index')->with([
            'alliance' => $userAlliance,
            'allianceService' => $allianceService,
            'membership' => $membership,
            'userRank' => $userRank,
            'members' => $members,
            'pendingApplications' => $pendingApplications,
            'isInAlliance' => $userAlliance !== null,
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
     * AJAX endpoint for alliance creation page
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
                'title' => 'OGameX - Create Alliance',
                'url' => route('alliance.create'),
            ],
            'serverTime' => time(),
            'target' => 'alliance/alliance_create',
        ]);
    }

    /**
     * Store a newly created alliance
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function store(Request $request, PlayerService $player): RedirectResponse
    {
        $request->validate([
            'tag' => 'required|string|min:3|max:8|unique:alliances,tag',
            'name' => 'required|string|min:3|max:64',
            'description' => 'nullable|string|max:5000',
            'logo' => 'nullable|url|max:255',
            'external_url' => 'nullable|url|max:255',
            'application_text' => 'nullable|string|max:5000',
        ]);

        try {
            // Check if user is already in an alliance
            if (AllianceMemberService::isInAlliance($player->getUser())) {
                return redirect()->route('alliance.index')->with('error', 'You are already a member of an alliance.');
            }

            $alliance = AllianceService::createAlliance([
                'tag' => $request->input('tag'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'logo' => $request->input('logo'),
                'external_url' => $request->input('external_url'),
                'application_text' => $request->input('application_text'),
                'open_for_applications' => $request->input('open_for_applications', true),
            ], $player->getUser());

            return redirect()->route('alliance.index')->with('success', 'Alliance created successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show alliance search page
     *
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        $query = $request->input('query', '');
        $results = collect();

        if ($query) {
            $results = AllianceService::search($query);
        }

        return view('ingame.alliance.search')->with([
            'query' => $query,
            'results' => $results,
        ]);
    }

    /**
     * Show a specific alliance's public page
     *
     * @param int $id
     * @param PlayerService $player
     * @return View
     */
    public function show(int $id, PlayerService $player): View
    {
        $allianceService = resolve(AllianceService::class, ['alliance_id' => $id]);

        if (!$allianceService->exists()) {
            abort(404, 'Alliance not found');
        }

        $alliance = $allianceService->getAlliance();
        $isMember = $allianceService->isMember($player->getId());
        $canApply = !AllianceMemberService::isInAlliance($player->getUser());

        // Check if user has a pending application
        $hasPendingApplication = AllianceApplication::where('alliance_id', $id)
            ->where('user_id', $player->getId())
            ->where('status', 'pending')
            ->exists();

        return view('ingame.alliance.show')->with([
            'alliance' => $alliance,
            'allianceService' => $allianceService,
            'isMember' => $isMember,
            'canApply' => $canApply && $alliance->open_for_applications,
            'hasPendingApplication' => $hasPendingApplication,
            'memberCount' => $allianceService->getMemberCount(),
        ]);
    }

    /**
     * Apply to join an alliance
     *
     * @param Request $request
     * @param int $id
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function apply(Request $request, int $id, PlayerService $player): RedirectResponse
    {
        $request->validate([
            'application_text' => 'nullable|string|max:2000',
        ]);

        try {
            $alliance = Alliance::findOrFail($id);
            AllianceMemberService::applyToAlliance(
                $alliance,
                $player->getUser(),
                $request->input('application_text')
            );

            return redirect()->route('alliance.show', $id)->with('success', 'Application submitted successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Accept an application
     *
     * @param int $applicationId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function acceptApplication(int $applicationId, PlayerService $player): RedirectResponse
    {
        try {
            $application = AllianceApplication::findOrFail($applicationId);
            $allianceService = resolve(AllianceService::class, ['alliance_id' => $application->alliance_id]);

            // Check permission
            if (!$allianceService->hasPermission($player->getId(), 'can_accept_applications')) {
                return redirect()->back()->with('error', 'You do not have permission to accept applications.');
            }

            AllianceMemberService::acceptApplication($application, $player->getUser());

            return redirect()->route('alliance.index')->with('success', 'Application accepted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an application
     *
     * @param int $applicationId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function rejectApplication(int $applicationId, PlayerService $player): RedirectResponse
    {
        try {
            $application = AllianceApplication::findOrFail($applicationId);
            $allianceService = resolve(AllianceService::class, ['alliance_id' => $application->alliance_id]);

            // Check permission
            if (!$allianceService->hasPermission($player->getId(), 'can_accept_applications')) {
                return redirect()->back()->with('error', 'You do not have permission to reject applications.');
            }

            AllianceMemberService::rejectApplication($application, $player->getUser());

            return redirect()->route('alliance.index')->with('success', 'Application rejected.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show alliance management page (for leaders)
     *
     * @param PlayerService $player
     * @return View
     */
    public function manage(PlayerService $player): View
    {
        $userAlliance = AllianceMemberService::getUserAlliance($player->getUser());

        if (!$userAlliance) {
            abort(404, 'You are not a member of any alliance.');
        }

        $allianceService = resolve(AllianceService::class, ['alliance_id' => $userAlliance->id]);

        // Check permission
        if (!$allianceService->hasPermission($player->getId(), 'can_edit_alliance')) {
            abort(403, 'You do not have permission to manage this alliance.');
        }

        return view('ingame.alliance.manage')->with([
            'alliance' => $userAlliance,
            'allianceService' => $allianceService,
        ]);
    }

    /**
     * Update alliance information
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function update(Request $request, PlayerService $player): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3|max:64',
            'description' => 'nullable|string|max:5000',
            'logo' => 'nullable|url|max:255',
            'external_url' => 'nullable|url|max:255',
            'internal_text' => 'nullable|string|max:10000',
            'application_text' => 'nullable|string|max:5000',
            'open_for_applications' => 'boolean',
        ]);

        try {
            $userAlliance = AllianceMemberService::getUserAlliance($player->getUser());

            if (!$userAlliance) {
                return redirect()->route('alliance.index')->with('error', 'You are not a member of any alliance.');
            }

            $allianceService = resolve(AllianceService::class, ['alliance_id' => $userAlliance->id]);

            // Check permission
            if (!$allianceService->hasPermission($player->getId(), 'can_edit_alliance')) {
                return redirect()->route('alliance.index')->with('error', 'You do not have permission to edit this alliance.');
            }

            $allianceService->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'logo' => $request->input('logo'),
                'external_url' => $request->input('external_url'),
                'internal_text' => $request->input('internal_text'),
                'application_text' => $request->input('application_text'),
                'open_for_applications' => $request->input('open_for_applications', false),
            ]);

            return redirect()->route('alliance.manage')->with('success', 'Alliance updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Kick a member from the alliance
     *
     * @param int $memberId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function kickMember(int $memberId, PlayerService $player): RedirectResponse
    {
        try {
            $member = AllianceMember::findOrFail($memberId);
            $allianceService = resolve(AllianceService::class, ['alliance_id' => $member->alliance_id]);

            // Check permission
            if (!$allianceService->hasPermission($player->getId(), 'can_kick')) {
                return redirect()->back()->with('error', 'You do not have permission to kick members.');
            }

            AllianceMemberService::kickMember($member);

            return redirect()->route('alliance.index')->with('success', 'Member kicked successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Leave the alliance
     *
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function leave(PlayerService $player): RedirectResponse
    {
        try {
            AllianceMemberService::leaveAlliance($player->getUser());
            return redirect()->route('alliance.index')->with('success', 'You have left the alliance.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Disband the alliance (founder only)
     *
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function disband(PlayerService $player): RedirectResponse
    {
        try {
            $userAlliance = AllianceMemberService::getUserAlliance($player->getUser());

            if (!$userAlliance) {
                return redirect()->route('alliance.index')->with('error', 'You are not a member of any alliance.');
            }

            // Only founder can disband
            if ($userAlliance->founder_id !== $player->getId()) {
                return redirect()->route('alliance.index')->with('error', 'Only the founder can disband the alliance.');
            }

            $allianceService = resolve(AllianceService::class, ['alliance_id' => $userAlliance->id]);
            $allianceService->delete();

            return redirect()->route('alliance.index')->with('success', 'Alliance disbanded successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
