<thick-headline>
    <thick-headline-header>
        <p>{{ $universe_name }}</p>
    </thick-headline-header>
    <thick-headline-background></thick-headline-background>
</thick-headline>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="acs_active"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.acs_enabled') }}</span>
    <div class="emoji {{ $alliance_combat_system_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="darkmatter_bonus"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.dm_bonus') }} {{ $dark_matter_bonus }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_defense"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.debris_defense') }} +{{ $debris_field_from_defense }}%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_fleet"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.debris_ships') }} +{{ $debris_field_from_ships }}%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_deuterium"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.debris_deuterium') }}</span>
    <div class="emoji {{ $debris_field_deuterium_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="deuterium_save"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.fleet_deut_reduction') }} 30%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_war"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.fleet_speed_war') }} x{{ $fleet_speed_war }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_holding"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.fleet_speed_holding') }} x{{ $fleet_speed_holding }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_peace"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.fleet_speed_peace') }} x{{ $fleet_speed_peaceful }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="ignore_empty"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.ignore_empty') }}</span>
    <div class="emoji {{ $ignore_empty_systems_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="ignore_inactive"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.ignore_inactive') }}</span>
    <div class="emoji {{ $ignore_inactive_systems_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="number_galaxies"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.num_galaxies') }} {{ $number_of_galaxies }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="planetfield_bonus"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.planet_field_bonus') }} {{ $planet_fields_bonus }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="speedfactor"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.dev_speed') }} x{{ $economy_speed }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="speedfactor_research"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.research_speed') }} x{{ $research_speed }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="darkmatter_bonus"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.dm_regen_enabled') }}</span>
    <div class="emoji {{ $dark_matter_regen_enabled ? 'activated' : 'deactivated' }}"></div>
</div>
@if($dark_matter_regen_enabled)
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="darkmatter_bonus"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.dm_regen_amount') }} {{ number_format($dark_matter_regen_amount) }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="darkmatter_bonus"></server-settings-icon>
    <span>{{ __('t_ingame.serversettings_overlay.dm_regen_period') }} {{ number_format($dark_matter_regen_period / 86400) }} {{ __('t_ingame.serversettings_overlay.days') }}</span>
</div>
@endif