<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\DarkMatterService;
use OGame\Services\OfficerService;
use OGame\Services\PlayerService;

class PremiumController extends OGameController
{
    public function __construct(
        private OfficerService $officerService,
        private DarkMatterService $darkMatterService
    ) {
        parent::__construct();
    }

    /**
     * Shows the premium/officers index page.
     */
    public function index(PlayerService $player): View
    {
        $this->setBodyId('premium');

        $user    = $player->getUser();
        $officer = $this->officerService->getOfficer($user);

        return view('ingame.premium.index', [
            'darkMatter' => $user->dark_matter,
            'officer'    => $officer,
        ]);
    }

    /**
     * AJAX: returns the HTML of the detail panel for a single officer.
     * Called by loadDetails() in the main layout through GET /ajax/premium?type=X
     */
    public function ajax(Request $request, PlayerService $player): string
    {
        $typeId  = (int) $request->input('type', 0);
        $user    = $player->getUser();
        $officer = $this->officerService->getOfficer($user);

        // Type 1 = Dark Matter (balance info only, no officer purchase).
        if ($typeId === 1) {
            return view('ingame.premium.detail-darkmatter', [
                'darkMatter' => $user->dark_matter,
            ])->render();
        }

        $officerKey = $this->officerService->getKeyFromTypeId($typeId);
        if ($officerKey === null) {
            return '';
        }

        $column    = $officerKey . '_until';
        $isActive  = $officer->isOfficerActive($officerKey);
        $expiresAt = $officer->$column;
        $costs     = OfficerService::COSTS[$officerKey] ?? [];

        return view('ingame.premium.detail-officer', [
            'officerKey'  => $officerKey,
            'typeId'      => $typeId,
            'isActive'    => $isActive,
            'expiresAt'   => $expiresAt,
            'costs'       => $costs,
            'darkMatter'  => $user->dark_matter,
            'benefitKeys' => OfficerService::BENEFIT_KEYS[$officerKey] ?? [],
        ])->render();
    }

    /**
     * POST: purchase/activate an officer and redirect back to the premium page.
     */
    public function purchase(Request $request, PlayerService $player): RedirectResponse
    {
        $typeId = (int) $request->input('type');
        $days   = (int) $request->input('days');
        $user   = $player->getUser();

        $officerKey = $this->officerService->getKeyFromTypeId($typeId);
        if ($officerKey === null) {
            return redirect()->route('premium.index')
                ->with('error', __('t_ingame.premium.invalid_officer_type'));
        }

        if (!in_array($days, OfficerService::DURATIONS, true)) {
            return redirect()->route('premium.index')
                ->with('error', __('t_ingame.premium.invalid_duration'));
        }

        $cost = $this->officerService->getCost($officerKey, $days);
        if (!$this->darkMatterService->canAfford($user, $cost)) {
            return redirect()->route('premium.index')
                ->with('error', __('t_ingame.premium.insufficient_dark_matter'));
        }

        try {
            $this->officerService->purchase($user, $officerKey, $days);
        } catch (Exception $e) {
            return redirect()->route('premium.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('premium.index')
            ->with('status', __('t_ingame.premium.purchase_success'));
    }
}
