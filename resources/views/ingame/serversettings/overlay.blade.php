<thick-headline>
    <thick-headline-header>
        <p>{{ $universe_name }}</p>
    </thick-headline-header>
    <thick-headline-background></thick-headline-background>
</thick-headline>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="acs_active"></server-settings-icon>
    <span>@lang('Alliance Combat System enabled')</span>
    <div class="emoji {{ $alliance_combat_system_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="darkmatter_bonus"></server-settings-icon>
    <span>@lang('Dark Matter bonus:') {{ $dark_matter_bonus }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_defense"></server-settings-icon>
    <span>@lang('Defensive structures in debris fields:') +{{ $debris_field_from_defense }}%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_fleet"></server-settings-icon>
    <span>@lang('Destroyed ships in debris fields:') +{{ $debris_field_from_ships }}%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="debris_deuterium"></server-settings-icon>
    <span>@lang('Deuterium in debris fields')</span>
    <div class="emoji {{ $debris_field_deuterium_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="deuterium_save"></server-settings-icon>
    <span>@lang('Fleet Deuterium consumption reduction:') 30%</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_holding"></server-settings-icon>
    <span>@lang('Fleet speed:') x{{ $fleet_speed }}</span>
</div>
{{-- <div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_peace"></server-settings-icon>
    <span>@lang('Peaceful fleet speed:') x4</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="fleetspeed_war"></server-settings-icon>
    <span>@lang('Belligerent fleet speed:') x4</span>
</div> --}}
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="ignore_empty"></server-settings-icon>
    <span>@lang('Empty systems are ignored')</span>
    <div class="emoji {{ $ignore_empty_systems_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="ignore_inactive"></server-settings-icon>
    <span>@lang('Inactive systems are ignored')</span>
    <div class="emoji {{ $ignore_inactive_systems_on ? 'activated' : 'deactivated' }}"></div>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="number_galaxies"></server-settings-icon>
    <span>@lang('Number of galaxies:') {{ $number_of_galaxies }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="planetfield_bonus"></server-settings-icon>
    <span>@lang('Planet field bonus:') {{ $planet_fields_bonus }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="speedfactor"></server-settings-icon>
    <span>@lang('Development speed:') x{{ $economy_speed }}</span>
</div>
<div class="technology-fullrow" style="z-index: 2 !important;">
    <server-settings-icon sq28 bordered class="speedfactor_research"></server-settings-icon>
    <span>@lang('Research speed:') x{{ $research_speed }}</span>
</div>