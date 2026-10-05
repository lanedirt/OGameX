<?php

namespace OGame\Enums;

/**
 * Enum that represents the types of military highscores.
 */
enum MilitaryHighscoreTypeEnum: int
{
    case built = 0;
    case destroyed = 1;
    case lost = 2;
}
