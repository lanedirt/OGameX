<?php

namespace OGame\Enums;

enum DarkMatterTransactionType: string
{
    case INITIAL_BONUS = 'initial_bonus';
    case REGENERATION = 'regeneration';
    case EXPEDITION = 'expedition';
    case COMMANDING_STAFF = 'commanding_staff';
    case PLAYER_CLASS = 'player_class';
    case MERCHANT = 'merchant';
    case PLANET_RELOCATION = 'planet_relocation';
    case SPEEDUP = 'speedup';
    case ADMIN_ADJUSTMENT = 'admin_adjustment';
    case HALVING = 'halving';
}
