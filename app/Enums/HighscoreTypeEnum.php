<?php

namespace OGame\Enums;

/**
 * Enum that represents the types of highscores.
 */
enum HighscoreTypeEnum: int
{
    case general = 0;
    case economy = 1;
    case research = 2;
    case military = 3;
}
