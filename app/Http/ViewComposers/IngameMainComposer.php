<?php

namespace OGame\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use OGame\Facades\AppUtil;
use OGame\Models\Message;
use OGame\Services\MessageService;
use OGame\Services\PlayerService;

/**
 * Class IngameMainComposer
 * @package OGame\Http\Composers
 *
 * Contains all preprocessor logic for parsing the ingame.layouts.main
 * blade theme file.
 */
class IngameMainComposer
{
    protected Request $request;
    protected PlayerService $player;
    protected MessageService $messageService;

    /**
     * IngameMainComposer constructor.
     *
     * Construct view composer and get all required data via dependency
     * injection.
     *
     * @param Request $request
     * @param PlayerService $player
     */
    public function __construct(Request $request, PlayerService $player, MessageService $messageService)
    {
        $this->request = $request;
        $this->player = $player;
        $this->messageService = $messageService;
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
        ];

        // Include body_id, which might have been set in the controller.
        $body_id = request()->attributes->get('body_id');

        // Get current locale
        $locale = app()->getLocale();

        $view->with([
            'unreadMessagesCount' => $this->messageService->getUnreadMessagesCount(),
            'resources' => $resources,
            'currentPlayer' => $this->player,
            'currentPlanet' => $this->player->planets->current(),
            'planets' => $this->player->planets,
            'body_id' => $body_id,
            'locale' => $locale,
        ]);
    }

}
