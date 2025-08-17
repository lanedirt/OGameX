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
        'body' => 'Your fleet is returning from :from to :to.

The fleet doesn\'t deliver goods.',
        ],

    // ------------------------
    'fleet_deployment_with_resources' => [
        'from' => 'Fleet Command',
        'subject' => 'Return of a fleet',
        'body' => 'One of your fleets from :from has reached :to and delivered its goods:

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
        'body' => 'Your fleet from :from reaches :to and delivers its goods:
Metal: :metal Crystal: :crystal Deuterium: :deuterium',
        ],

    // ------------------------
    'transport_received' => [
        'from' => 'Fleet Command',
        'subject' => 'Incoming fleet',
        'body' => 'An incoming fleet from :from has reached your planet :to and delivered its goods:
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
    // Expedition generic message parts
    'expedition_resources_captured' => ':resource_type :resource_amount have been captured.',
    'expedition_units_captured' => 'The following ships are now part of the fleet:',

    'expedition_unexplored_statement' => 'Entry from the communication officers logbook: It seems that this part of the universe has not been explored yet.',

    // Expedition Failed
    'expedition_failed' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionFailed class.
        'body' => [
            '1' => 'Due to a failure in the central computers of the flagship, the expedition mission had to be aborted. Unfortunately as a result of the computer malfunction, the fleet returns home empty handed.',
            '2' => 'Your expedition nearly ran into a neutron stars gravitation field and needed some time to free itself. Because of that a lot of Deuterium was consumed and the expedition fleet had to come back without any results.',
            '3' => 'For unknown reasons the expeditions jump went totally wrong. It nearly landed in the heart of a sun. Fortunately it landed in a known system, but the jump back is going to take longer than thought.',
            '4' => 'A failure in the flagships reactor core nearly destroys the entire expedition fleet. Fortunately the technicians were more than competent and could avoid the worst. The repairs took quite some time and forced the expedition to return without having accomplished its goal.',
            '5' => 'A living being made out of pure energy came aboard and induced all the expedition members into some strange trance, causing them to only gazed at the hypnotizing patterns on the computer screens. When most of them finally snapped out of the hypnotic-like state, the expedition mission needed to be aborted as they had way too little Deuterium.',
            '6' => 'The new navigation module is still buggy. The expeditions jump not only lead them in the wrong direction, but it used all the Deuterium fuel. Fortunately the fleets jump got them close to the departure planets moon. A bit disappointed the expedition now returns without impulse power. The return trip will take longer than expected.',
            '7' => 'Your expedition has learnt about the extensive emptiness of space. There was not even one small asteroid or radiation or particle that could have made this expedition interesting.',
            '8' => 'Well, now we know that those red, class 5 anomalies do not only have chaotic effects on the ships navigation systems but also generate massive hallucination on the crew. The expedition didn`t bring anything back.',
            '9' => 'Your expedition fleet made contact with a friendly alien race. They announced that they would send a representative with goods to trade to your worlds.',
            '10' => 'Your expedition took gorgeous pictures of a super nova. Nothing new could be obtained from the expedition, but at least there is good chance to win that "Best Picture Of The Universe" competition in next months issue of OGame magazine.',
            '11' => 'Your expedition fleet followed odd signals for some time. At the end they noticed that those signals where being sent from an old probe which was sent out generations ago to greet foreign species. The probe was saved and some museums of your home planet already voiced their interest.',
            '12' => 'Despite the first, very promising scans of this sector, we unfortunately returned empty handed.',
            '13' => 'Besides some quaint, small pets from a unknown marsh planet, this expedition brings nothing thrilling back from the trip.',
            '14' => 'The expedition`s flagship collided with a foreign ship when it jumped into the fleet without any warning. The foreign ship exploded and the damage to the flagship was substantial. The expedition cannot continue in these conditions, and so the fleet will begin to make its way back once the needed repairs have been carried out.',
            '15' => 'Our expedition team came across a strange colony that had been abandoned eons ago. After landing, our crew started to suffer from a high fever caused by an alien virus. It has been learned that this virus wiped out the entire civilization on the planet. Our expedition team is heading home to treat the sickened crew members. Unfortunately we had to abort the mission and we come home empty handed.',
            '16' => 'A strange computer virus attacked the navigation system shortly after parting our home system. This caused the expedition fleet to fly in circles. Needless to say that the expedition wasn`t really successful.',
        ],
    ],

    // Gain Resources
    'expedition_gain_resources' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionGainResources class.
        'body' => [
            '1' => 'On an isolated planetoid we found some easily accessible resources fields and harvested some successfully.',
            '2' => 'Your expedition discovered a small asteroid from which some resources could be harvested.',
            '3' => 'Your expedition found an ancient, fully loaded but deserted freighter convoy. Some of the resources could be rescued.',
            '4' => 'Your expedition fleet reports the discovery of a giant alien ship wreck. They were not able to learn from their technologies but they were able to divide the ship into its main components and made some useful resources out of it.',
            '5' => 'On a tiny moon with its own atmosphere your expedition found some huge raw resources storage. The crew on the ground is trying to lift and load that natural treasure.',
            '6' => 'Mineral belts around an unknown planet contained countless resources. The expedition ships are coming back and their storages are full!',
        ],
    ],

    // Gain Dark Matter
    'expedition_gain_dark_matter' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionGainDarkMatter class.
        'body' => [
            '1' => 'The expedition followed some odd signals to an asteroid. In the asteroids core a small amount of Dark Matter was found. The asteroid was taken and the explorers are attempting to extract the Dark Matter.',
            '2' => 'The expedition was able to capture and store some Dark Matter.',
            '3' => 'We met an odd alien on the shelf of a small ship who gave us a case with Dark Matter in exchange for some simple mathematical calculations.',
            '4' => 'We found the remains of an alien ship. We found a little container with some Dark Matter on a shelf in the cargo hold!',
            '5' => 'Our expedition made first contact with a special race. It looks as though a creature made of pure energy, who named himself Legorian, flew through the expedition ships and then decided to help our underdeveloped species. A case containing Dark Matter materialized at the bridge of the ship!',
            '6' => 'Our expedition took over a ghost ship which was transporting a small amount of Dark Matter. We didn`t find any hints of what happened to the original crew of the ship, but our technicians where able to rescue the Dark Matter.',
            '7' => 'Our expedition accomplished a unique experiment. They were able to harvest Dark Matter from a dying star.',
            '8' => 'Our expedition located a rusty space station, which seemed to have been floating uncontrolled through outer space for a long time. The station itself was totally useless, however, it was discovered that some Dark Matter is stored in the reactor. Our technicians are trying to save as much as they can.',
        ],
    ],

    // Gain Ships
    'expedition_gain_ships' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionGainShips class.
        'body' => [
            '1' => 'Our expedition found a planet which was almost destroyed during a certain chain of wars. There are different ships floating around in the orbit. The technicians are trying to repair some of them. Maybe we will also get information about what happened here.',
            '2' => 'We found a deserted pirate station. There are some old ships lying in the hangar. Our technicians are figuring out whether some of them are still useful or not.',
            '3' => 'Your expedition ran into the shipyards of a colony that was deserted eons ago. In the shipyards hangar they discover some ships that could be salvaged. The technicians are trying to get some of them to fly again.',
            '4' => 'We came across the remains of a previous expedition! Our technicians will try to get some of the ships to work again.',
            '5' => 'Our expedition ran into an old automatic shipyard. Some of the ships are still in the production phase and our technicians are currently trying to reactivate the yards energy generators.',
            '6' => 'We found the remains of an armada. The technicians directly went to the almost intact ships to try to get them to work again.',
            '7' => 'We found the planet of an extinct civilization. We are able to see a giant intact space station, orbiting. Some of your technicians and pilots went to the surface looking for some ships which could still be used.',
        ],
    ],

    // Gain Item
    'expedition_gain_item' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionGainItem class.
        'body' => [
            '1' => 'A fleeing fleet left an item behind, in order to distract us in aid of their escape.',
        ],
    ],

    // Failed and Speedup
    'expedition_failed_and_speedup' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionSpeedup class.
        'body' => [
            '1' => 'Your expeditions doesn`t report any anomalies in the explored sector. But the fleet ran into some solar wind while returning. This resulted in the return trip being expedited. Your expedition returns home a bit earlier.',
            '2' => 'The new and daring commander successfully traveled through an unstable wormhole to shorten the flight back! However, the expedition itself didn`t bring anything new.',
            '3' => 'An unexpected back coupling in the energy spools of the engines hastened the expeditions return, it returns home earlier than expected. First reports tell they do not have anything thrilling to account for.',
        ],
    ],

    // Failure and Delay
    'expedition_failed_and_delay' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionDelay class.
        'body' => [
            '1' => 'Your expedition went into a sector full of particle storms. This set the energy stores to overload and most of the ships` main systems crashed. Your mechanics were able to avoid the worst, but the expedition is going to return with a big delay.',
            '2' => 'Your navigator made a grave error in his computations that caused the expeditions jump to be miscalculated. Not only did the fleet miss the target completely, but the return trip will take a lot more time than originally planned.',
            '3' => 'The solar wind of a red giant ruined the expeditions jump and it will take quite some time to calculate the return jump. There was nothing besides the emptiness of space between the stars in that sector. The fleet will return later than expected.',
        ],
    ],

    // Battle
    'expedition_battle' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionBattle class.
        'body' => [
            '1' => 'Some primitive barbarians are attacking us with spaceships that can`t even be named as such. If the fire gets serious we will be forced to fire back.',
            '2' => 'We needed to fight some pirates which were, fortunately, only a few.',
            '3' => 'We caught some radio transmissions from some drunk pirates. Seems like we will be under attack soon.',
            '4' => 'Our expedition was attacked by a small group of unknown ships!',
            '5' => 'Some really desperate space pirates tried to capture our expedition fleet.',
            '6' => 'Some exotic looking ships attacked the expedition fleet without warning!',
            '7' => 'Your expedition fleet had an unfriendly first contact with an unknown species.',
        ],
    ],

    // Loss of Fleet
    'expedition_loss_of_fleet' => [
        'from' => 'Fleet Command',
        'subject' => 'Expedition Result',
        // An expedition message can have different variations which are parsed by the ExpeditionLossOfFleet class.
        'body' => [
            '1' => 'A core meltdown of the lead ship leads to a chain reaction, which destroys the entire expedition fleet in a spectacular explosion.',
        ],
    ],
];
