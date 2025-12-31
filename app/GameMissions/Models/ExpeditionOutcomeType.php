<?php

namespace OGame\GameMissions\Models;

/**
 * Expedition mission outcome types.
 */
enum ExpeditionOutcomeType: string
{
    /**
     * The expedition mission found resources.
     */
    case GainResources = 'expedition_gain_resources';

    /**
     * The expedition mission found dark matter.
     */
    case GainDarkMatter = 'expedition_gain_dark_matter';

    /**
     * The expedition mission found units.
     */
    case GainShips = 'expedition_gain_ships';

    /**
     * The expedition mission found items.
     */
    case GainItems = 'expedition_gain_item';

    /**
     * The expedition mission found merchant trade.
     */
    case GainMerchantTrade = 'expedition_gain_merchant_trade';

    /**
     * The expedition mission failed.
     */
    case Failed = 'expedition_failed';

    /**
     * The expedition mission failed and the return trip was speeded up.
     */
    case FailedAndSpeedup = 'expedition_failed_and_speedup';

    /**
     * The expedition mission failed and the return trip was delayed.
     */
    case FailedAndDelay = 'expedition_failed_and_delay';

    /**
     * The expedition mission lost the fleet.
     */
    case LossOfFleet = 'expedition_loss_of_fleet';

    /**
     * The expedition mission encountered space pirates and a battle ensued.
     */
    case BattlePirates = 'expedition_battle_pirates';

    /**
     * The expedition mission encountered an alien fleet and a battle ensued.
     */
    case BattleAliens = 'expedition_battle_aliens';

    /**
     * Get the setting key for the expedition outcome type.
     *
     * @return string
     */
    public function getSettingKey(): string
    {
        return $this->value;
    }
}
