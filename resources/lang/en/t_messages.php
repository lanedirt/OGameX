<?php

return [
    // ------------------------
    'welcome_message' => [
        'from' => 'OGameX',
        'subject' => 'Welcome to OGameX!',
        'body' => 'Greetings Emperor :player!

Congratulations on starting your illustrious career. I will be here to guide you through your first steps.

On the left you can see the menu which allows you to supervise and govern your galactic empire.

You’ve already seen the Overview. Resources and Facilities allow you to construct buildings to help you expand your empire. Start by building a Solar Plant to harvest energy for your mines.

Then expand your Metal Mine and Crystal Mine to produce vital resources. Otherwise, simply take a look around for yourself. You’ll soon feel well at home, I’m sure.

You can find more help, tips and tactics here:

Discord Chat: Discord Server
Forum: OGameX Forum
Support: Game Support

You’ll only find current announcements and changes to the game in the forums.


Now you’re ready for the future. Good luck!

This message will be deleted in 7 days.',
    ],

    // ------------------------
    'return_of_fleet_with_resources' => [
        'from' => 'Fleet Command',
        'subject' => 'Return of a fleet',
        'body' => 'Your fleet is returning from :from to :to and delivered its goods:

Metal: :metal
Crystal: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'return_of_fleet' => [
        'from' => 'Fleet Command',
        'subject' => 'Return of a fleet',
        'body' => 'Your fleet is returning from planet :from to planet :to.

The fleet doesn\'t deliver goods.',
        ],

    // ------------------------
    'fleet_deployment_with_resources' => [
        'from' => 'Fleet Command',
        'subject' => 'Return of a fleet',
        'body' => 'One of your fleets from planet :from has reached planet :to and delivered its goods:

Metal: :metal
Crystal: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'fleet_deployment' => [
        'from' => 'Fleet Command',
        'subject' => 'Return of a fleet',
        'body' => 'One of your fleets from :from has reached :to. The fleet doesn`t deliver goods.',
        ],

    // ------------------------
    'transport_arrived' => [
        'from' => 'Fleet Command',
        'subject' => 'Reaching a planet',
        'body' => 'Your fleet from planet :from reaches the planet :to and delivers its goods:
Metal: :metal Crystal: :crystal Deuterium: :deuterium',
        ],

    // ------------------------
    'transport_received' => [
        'from' => 'Fleet Command',
        'subject' => 'Incoming fleet',
        'body' => 'An incoming fleet from planet :from has reached your planet :to and delivered its goods:
Metal: :metal Crystal: :crystal Deuterium: :deuterium',
    ],

    // ------------------------
    'colony_established' => [
        'from' => 'Fleet Command',
        'subject' => 'Settlement Report',
        'body' => 'The fleet has arrived at the assigned coordinates :coordinates, found a new planet there and are beginning to develop upon it immediately.',
    ],

    // ------------------------
    'colony_establish_fail_astrophysics' => [
        'from' => 'Settlers',
        'subject' => 'Settlement Report',
        'body' => 'The fleet has arrived at assigned coordinates :coordinates and ascertains that the planet is viable for colonisation. Shortly after starting to develop the planet, the colonists realise that their knowledge of astrophysics is not sufficient to complete the colonisation of a new planet.',
    ],

    // ------------------------
    'espionage_report' => [
        'from' => 'Fleet Command',
        'subject' => 'Espionage report from :planet',
    ],

    // ------------------------
    'battle_report' => [
        'from' => 'Fleet Command',
        'subject' => 'Combat report :planet',
    ],

    // ------------------------
    'debris_field_harvest' => [
        'from' => 'Fleet',
        'subject' => 'Harvesting report from DF on :coordinates',
        'body' => 'Your :ship_name (:ship_amount ships) have a total storage capacity of :storage_capacity. At the target :to, :metal Metal, :crystal Crystal and :deuterium Deuterium are floating in space. You have harvested :harvested_metal Metal, :harvested_crystal Crystal and :harvested_deuterium Deuterium.',
    ],

    // ------------------------
];
