<?php

namespace OGame\Enums;

enum CharacterClass: int
{
    case COLLECTOR = 1;
    case GENERAL = 2;
    case DISCOVERER = 3;

    /**
     * Get the display name of the character class.
     */
    public function getName(): string
    {
        return match($this) {
            self::COLLECTOR => 'Collector',
            self::GENERAL => 'General',
            self::DISCOVERER => 'Discoverer',
        };
    }

    /**
     * Get the machine name (CSS class) of the character class.
     */
    public function getMachineName(): string
    {
        return match($this) {
            self::COLLECTOR => 'miner',
            self::GENERAL => 'warrior',
            self::DISCOVERER => 'explorer',
        };
    }

    /**
     * Get the class-specific ship ID.
     */
    public function getClassShipId(): int
    {
        return match($this) {
            self::COLLECTOR => 217, // Crawler
            self::GENERAL => 218, // Reaper
            self::DISCOVERER => 219, // Pathfinder
        };
    }

    /**
     * Get the class-specific ship name.
     */
    public function getClassShipName(): string
    {
        return match($this) {
            self::COLLECTOR => 'Crawler',
            self::GENERAL => 'Reaper',
            self::DISCOVERER => 'Pathfinder',
        };
    }

    /**
     * Get the cost to change to this class (in Dark Matter).
     */
    public function getChangeCost(): int
    {
        return 500000;
    }

    /**
     * Get all character class bonuses as an array.
     *
     * @return array<int, string>
     */
    public function getBonuses(): array
    {
        return match($this) {
            self::COLLECTOR => [
                '+25% mine production',
                '+10% energy production',
                '+100% speed for Transporters',
                '+25% cargo bay for Transporters',
                '+50% Crawler bonus',
                '+10% more usable Crawlers with Geologist',
                'Overload the Crawlers up to 150%',
                '+10% discount on acceleration (building)',
            ],
            self::GENERAL => [
                '+100% speed for combat ships',
                '+100% speed for Recyclers',
                '-50% deuterium consumption for all ships',
                '+20% cargo bay for Recyclers and Pathfinders',
                'A small chance to immediately destroy a Deathstar once in a battle using a light fighter.',
                'Wreckage at attack (transport to starting planet)',
                '+2 combat research levels',
                '+2 fleet slots',
                '+5 additional Moon Fields',
                'Detailed fleet speed settings',
                '+10% discount on acceleration (shipyard)',
            ],
            self::DISCOVERER => [
                '-25% research time',
                'Increased gain on successful expeditions',
                '+10% larger planets on colonisation',
                'Debris fields created on expeditions will be visible in the Galaxy view.',
                '+2 expeditions',
                '-50% chance of expedition enemies',
                '+20% phalanx range',
                '75% loot from inactive players',
                '+10% discount on acceleration (research)',
            ],
        };
    }

    /**
     * Get the ship description for this class.
     */
    public function getShipDescription(): string
    {
        return match($this) {
            self::COLLECTOR => 'The Crawler is a large trench vehicle that increases the production of mines and synthesizers. It is more agile than it looks but it is not particularly robust. Each Crawler increases metal production by 0.02%, crystal production by 0.02% and Deuterium production by 0.02%. As a collector, production also increases. The maximum total bonus depends on the overall level of your mines.',
            self::GENERAL => "There's hardly anything more destructive than a ship of the Reaper class. These vessels combine fire power, strong shields, speed and capacity along with the unique ability to mine a portion of the created debris field directly after a battle. However this ability doesn't apply to combat against pirates or aliens.",
            self::DISCOVERER => 'Pathfinders are fast and spacious. Their construction method is optimised for pushing into unknown territory. They are capable of discovering and mining debris fields during expeditions. Additionally they can find items out on expeditions. Total yield also increases.',
        };
    }
}
