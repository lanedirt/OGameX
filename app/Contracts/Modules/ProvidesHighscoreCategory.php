<?php

namespace OGame\Contracts\Modules;

/**
 * Contract for modules that add custom highscore categories.
 *
 * Register implementations through the Extensions facade:
 *
 *   Extensions::module('lifeforms', function (ModuleExtension $module): void {
 *       $module->highscoreCategory(PopulationHighscore::class);
 *   });
 */
interface ProvidesHighscoreCategory
{
    /**
     * Return the unique string identifier for this highscore category.
     */
    public function getCategoryId(): string;

    /**
     * Return the display name for this highscore category.
     */
    public function getCategoryLabel(): string;

    /**
     * Return the score value for the given user ID.
     */
    public function getScoreForUser(int $userId): int;
}
