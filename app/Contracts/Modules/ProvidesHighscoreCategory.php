<?php

namespace OGame\Contracts\Modules;

/**
 * Contract for modules that add custom highscore categories.
 *
 * Register implementations via app()->tag() in bootModule():
 *   app()->tag(MyHighscoreCategory::class, 'module.highscore_categories');
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
