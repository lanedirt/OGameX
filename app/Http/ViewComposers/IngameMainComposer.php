<?php

namespace OGame\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use OGame\Facades\AppUtil;
use OGame\Models\Alliance;
use OGame\Models\AllianceMember;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\ChatService;
use OGame\Services\FleetMissionService;
use OGame\Services\HighscoreService;
use OGame\Services\MessageService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

/**
 * Class IngameMainComposer
 * @package OGame\Http\Composers
 *
 * Contains all preprocessor logic for parsing the ingame.layouts.main
 * blade theme file.
 */
class IngameMainComposer
{
    /**
     * IngameMainComposer constructor.
     *
     * Construct view composer and get all required data via dependency
     * injection.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param MessageService $messageService
     * @param SettingsService $settingsService
     * @param FleetMissionService $fleetMissionService
     * @param HighscoreService $highscoreService
     * @param BuddyService $buddyService
     */
    public function __construct(private Request $request, private PlayerService $player, private MessageService $messageService, private SettingsService $settingsService, private FleetMissionService $fleetMissionService, private HighscoreService $highscoreService, private BuddyService $buddyService, private ChatService $chatService)
    {
    }

    /**
     * Compose the view and pass any required variables.
     *
     * @param View $view
     */
    public function compose(View $view): void
    {
        $current_planet = $this->player->planets->current();
        $resources = [
            'metal' => [
                'amount' => $current_planet->metal()->get(),
                'amount_formatted' => $current_planet->metal()->getFormattedLong(),
                'production_hour' => $current_planet->getMetalProductionPerHour(),
                'production_hour_formatted' => AppUtil::formatNumber($current_planet->getMetalProductionPerHour()),
                'production_second' => $current_planet->getMetalProductionPerSecond(),
                'storage' => $current_planet->metalStorage()->get(),
                'storage_formatted' => $current_planet->metalStorage()->getFormattedLong(),
                'storage_almost_full' => ($current_planet->metal()->get() >= ($current_planet->metalStorage()->get() * 0.9) && $current_planet->metal()->get() < $current_planet->metalStorage()->get()) ? true : false,
            ],
            'crystal' => [
                'amount' => $current_planet->crystal()->get(),
                'amount_formatted' => $current_planet->crystal()->getFormattedLong(),
                'production_hour' => $current_planet->getCrystalProductionPerHour(),
                'production_hour_formatted' => AppUtil::formatNumber($current_planet->getCrystalProductionPerHour()),
                'production_second' => $current_planet->getCrystalProductionPerSecond(),
                'storage' => $current_planet->crystalStorage()->get(),
                'storage_formatted' => $current_planet->crystalStorage()->getFormattedLong(),
                'storage_almost_full' => ($current_planet->crystal()->get() >= ($current_planet->crystalStorage()->get() * 0.9) && $current_planet->crystal()->get() < $current_planet->crystalStorage()->get()) ? true : false,
            ],
            'deuterium' => [
                'amount' => $current_planet->deuterium()->get(),
                'amount_formatted' => $current_planet->deuterium()->getFormattedLong(),
                'production_hour' => $current_planet->getDeuteriumProductionPerHour(),
                'production_hour_formatted' => AppUtil::formatNumber($current_planet->getDeuteriumProductionPerHour()),
                'production_second' => $current_planet->getDeuteriumProductionPerSecond(),
                'storage' => $current_planet->deuteriumStorage()->get(),
                'storage_formatted' => $current_planet->deuteriumStorage()->getFormattedLong(),
                'storage_almost_full' => ($current_planet->deuterium()->get() >= ($current_planet->deuteriumStorage()->get() * 0.9) && $current_planet->deuterium()->get() < $current_planet->deuteriumStorage()->get()) ? true : false,
            ],
            'energy' => [
                'amount' => $current_planet->energy()->get(),
                'amount_formatted' => $current_planet->energy()->getFormattedLong(),
                'production' => $current_planet->energyProduction()->get(),
                'production_formatted' => $current_planet->energyProduction()->getFormattedLong(),
                'consumption' => $current_planet->energyConsumption()->get(),
                'consumption_formatted' => $current_planet->energyConsumption()->getFormattedLong(),
            ],
            'darkmatter' => [
                'amount' => $this->player->getDarkMatter(),
                'amount_formatted' => AppUtil::formatNumber($this->player->getDarkMatter()),
            ],
        ];

        // Include body_id, which might have been set in the controller.
        $body_id = $this->request->attributes->get('body_id');

        // Get current locale
        $locale = App::getLocale();

        $currentPlayerIsAdmin = $this->player->isAdmin();
        $highscoreAdminVisible = $this->highscoreService->isAdminVisibleInHighscore();

        // Show "-" for admin rank if setting is disabled, otherwise show actual rank
        if ($currentPlayerIsAdmin && !$highscoreAdminVisible) {
            $highscoreRank = '-';
        } else {
            $highscoreRank = Cache::remember('player-highscore' . $this->player->getId(), now()->addMinutes(5), function () {
                return $this->highscoreService->getHighscorePlayerRank($this->player);
            });
        }

        $view->with([
            'underAttack' => $this->fleetMissionService->currentPlayerUnderAttack(),
            'unreadMessagesCount' => $this->messageService->getUnreadMessagesCount(),
            'buddyRequestCount' => $this->buddyService->getUnreadRequestsCount((int) auth()->id()),
            'onlineBuddiesCount' => $this->getOnlineContactsCount(),
            'unreadChatCount' => $this->chatService->getTotalUnreadMessageCount((int) auth()->id()),
            'resources' => $resources,
            'currentPlayer' => $this->player,
            'currentPlanet' => $this->player->planets->current(),
            'planets' => $this->player->planets,
            'highscoreRank' => $highscoreRank,
            'settings' => $this->settingsService,
            'body_id' => $body_id,
            'locale' => $locale,
        ]);
    }

    /**
     * Get the total number of online contacts (buddies + alliance members).
     */
    private function getOnlineContactsCount(): int
    {
        $userId = (int) auth()->id();

        // Count online buddies
        $onlineBuddies = $this->buddyService->getOnlineBuddiesCount($userId);

        // Count online alliance members (excluding self and buddies)
        $onlineAllianceMembers = 0;
        $user = User::find($userId);
        if ($user && $user->alliance_id && $user->alliance) {
            /** @var Alliance $alliance */
            $alliance = $user->alliance;
            $buddyUserIds = $this->buddyService->getBuddies($userId)->map(function ($req) use ($userId) {
                return $req->sender_user_id === $userId ? $req->receiver_user_id : $req->sender_user_id;
            })->toArray();

            $onlineAllianceMembers = $alliance->members()
                ->where('user_id', '!=', $userId)
                ->whereNotIn('user_id', $buddyUserIds)
                ->with('user')
                ->get()
                ->filter(function ($member) {
                    /** @var AllianceMember $member */
                    return $member->user && $member->user->isOnline();
                })
                ->count();
        }

        return $onlineBuddies + $onlineAllianceMembers;
    }
}
