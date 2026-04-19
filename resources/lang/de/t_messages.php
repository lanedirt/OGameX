<?php

return [
    // ------------------------
    'welcome_message' => [
        'from' => 'OGameX',
        'subject' => 'Willkommen bei OGameX!',
        'body' => 'Grüße, Imperator :player!

Herzlichen Glückwunsch zum Beginn deiner ruhmreichen Karriere. Ich werde hier sein, um dich durch deine ersten Schritte zu führen.

Auf der linken Seite siehst du das Menü, mit dem du dein galaktisches Imperium überwachen und regieren kannst.

Du hast bereits die Übersicht gesehen. Unter Rohstoffe und Anlagen kannst du Gebäude errichten, um dein Imperium auszubauen. Beginne mit dem Bau eines Solarkraftwerks, um Energie für deine Minen zu gewinnen.

Baue dann deine Metallmine und Kristallmine aus, um lebenswichtige Rohstoffe zu produzieren. Ansonsten schau dich einfach selbst um. Du wirst dich bestimmt schnell zurechtfinden.

Weitere Hilfe, Tipps und Taktiken findest du hier:

Discord Chat: Discord Server
Forum: OGameX Forum
Support: Spielsupport

Aktuelle Ankündigungen und Änderungen am Spiel findest du nur in den Foren.


Jetzt bist du bereit für die Zukunft. Viel Glück!

Diese Nachricht wird in 7 Tagen gelöscht.',
    ],

    // ------------------------
    'return_of_fleet_with_resources' => [
        'from' => 'Flottenkommando',
        'subject' => 'Rückkehr einer Flotte',
        'body' => 'Deine Flotte kehrt von :from nach :to zurück und hat ihre Güter geliefert:

Metall: :metal
Kristall: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'return_of_fleet' => [
        'from' => 'Flottenkommando',
        'subject' => 'Rückkehr einer Flotte',
        'body' => 'Deine Flotte kehrt von :from nach :to zurück.

Die Flotte liefert keine Güter.',
        ],

    // ------------------------
    'fleet_deployment_with_resources' => [
        'from' => 'Flottenkommando',
        'subject' => 'Rückkehr einer Flotte',
        'body' => 'Eine deiner Flotten von :from hat :to erreicht und ihre Güter geliefert:

Metall: :metal
Kristall: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'fleet_deployment' => [
        'from' => 'Flottenkommando',
        'subject' => 'Rückkehr einer Flotte',
        'body' => 'Eine deiner Flotten von :from hat :to erreicht. Die Flotte liefert keine Güter.',
        ],

    // ------------------------
    'transport_arrived' => [
        'from' => 'Flottenkommando',
        'subject' => 'Ankunft auf einem Planeten',
        'body' => 'Deine Flotte von :from erreicht :to und liefert ihre Güter:
Metall: :metal Kristall: :crystal Deuterium: :deuterium',
        ],

    // ------------------------
    'transport_received' => [
        'from' => 'Flottenkommando',
        'subject' => 'Eingehende Flotte',
        'body' => 'Eine eingehende Flotte von :from hat deinen Planeten :to erreicht und ihre Güter geliefert:
Metall: :metal Kristall: :crystal Deuterium: :deuterium',
    ],

    // ------------------------
    'acs_defend_arrival_host' => [
        'from' => 'Weltraumüberwachung',
        'subject' => 'Flotte hält an',
        'body' => 'Eine Flotte ist bei :to angekommen.',
    ],

    // ------------------------
    'acs_defend_arrival_sender' => [
        'from' => 'Flottenkommando',
        'subject' => 'Flotte hält an',
        'body' => 'Eine Flotte ist bei :to angekommen.',
    ],

    // ------------------------
    'colony_established' => [
        'from' => 'Flottenkommando',
        'subject' => 'Siedlungsbericht',
        'body' => 'Die Flotte hat die zugewiesenen Koordinaten :coordinates erreicht, dort einen neuen Planeten gefunden und beginnt sofort mit dessen Erschließung.',
    ],

    // ------------------------
    'colony_establish_fail_astrophysics' => [
        'from' => 'Siedler',
        'subject' => 'Siedlungsbericht',
        'body' => 'Die Flotte hat die zugewiesenen Koordinaten :coordinates erreicht und festgestellt, dass der Planet zur Kolonisation geeignet ist. Kurz nach Beginn der Erschließung des Planeten stellen die Kolonisten fest, dass ihre Kenntnisse in Astrophysik nicht ausreichen, um die Kolonisation eines neuen Planeten abzuschließen.',
    ],

    // ------------------------
    'espionage_report' => [
        'from' => 'Flottenkommando',
        'subject' => 'Spionagebericht von :planet',
    ],

    // ------------------------
    'espionage_detected' => [
        'from' => 'Flottenkommando',
        'subject' => 'Spionagebericht von Planet :planet',
        'body' => "Eine fremde Flotte vom Planeten :planet (:attacker_name) wurde in der Nähe deines Planeten gesichtet\n:defender\nGegenspionagewahrscheinlichkeit: :chance%",
    ],

    // ------------------------
    'battle_report' => [
        'from' => 'Flottenkommando',
        'subject' => 'Kampfbericht :planet',
    ],

      // ------------------------
    'fleet_lost_contact' => [
        'from' => 'Flottenkommando',
        'subject' => 'Kontakt zur angreifenden Flotte wurde verloren. :coordinates',
        'body' => '(Das bedeutet, sie wurde in der ersten Runde zerstört.)',
    ],

    // ------------------------
    'debris_field_harvest' => [
        'from' => 'Flotte',
        'subject' => 'Abbaubericht vom TF bei :coordinates',
        'body' => 'Deine :ship_name (:ship_amount Schiffe) haben eine Gesamtladekapazität von :storage_capacity. Beim Ziel :to schweben :metal Metall, :crystal Kristall und :deuterium Deuterium im All. Du hast :harvested_metal Metall, :harvested_crystal Kristall und :harvested_deuterium Deuterium geerntet.',
    ],

    // ------------------------
    // Expedition generic message parts
    'expedition_resources_captured' => ':resource_type :resource_amount wurden erbeutet.',
    'expedition_dark_matter_captured' => '(:dark_matter_amount Dunkle Materie)',
    'expedition_units_captured' => 'Folgende Schiffe sind nun Teil der Flotte:',

    'expedition_unexplored_statement' => 'Eintrag aus dem Logbuch des Kommunikationsoffiziers: Es scheint, als wäre dieser Teil des Universums noch nicht erforscht worden.',

    // Expedition Failed
    'expedition_failed' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Aufgrund eines Ausfalls in den Zentralcomputern des Flaggschiffs musste die Expeditionsmission abgebrochen werden. Leider kehrt die Flotte aufgrund des Computerfehlers mit leeren Händen nach Hause zurück.',
            '2' => 'Deine Expedition geriet beinahe in das Gravitationsfeld eines Neutronensterns und brauchte einige Zeit, um sich zu befreien. Dabei wurde viel Deuterium verbraucht und die Expeditionsflotte musste ohne Ergebnis zurückkehren.',
            '3' => 'Aus unbekannten Gründen ging der Sprung der Expedition völlig daneben. Sie landete fast im Herzen einer Sonne. Glücklicherweise landete sie in einem bekannten System, aber der Rücksprung wird länger dauern als gedacht.',
            '4' => 'Ein Ausfall im Reaktorkern des Flaggschiffs zerstörte beinahe die gesamte Expeditionsflotte. Glücklicherweise waren die Techniker mehr als kompetent und konnten das Schlimmste verhindern. Die Reparaturen nahmen einige Zeit in Anspruch und zwangen die Expedition zur Rückkehr, ohne ihr Ziel erreicht zu haben.',
            '5' => 'Ein Lebewesen aus reiner Energie kam an Bord und versetzte alle Expeditionsmitglieder in eine seltsame Trance, sodass sie nur noch auf die hypnotisierenden Muster auf den Computerbildschirmen starrten. Als die meisten endlich aus dem hypnoseartigen Zustand erwachten, musste die Expeditionsmission abgebrochen werden, da sie viel zu wenig Deuterium hatten.',
            '6' => 'Das neue Navigationsmodul ist noch fehlerhaft. Der Sprung der Expedition führte nicht nur in die falsche Richtung, sondern verbrauchte auch den gesamten Deuterium-Treibstoff. Glücklicherweise brachte der Sprung die Flotte in die Nähe des Mondes des Abflugplaneten. Etwas enttäuscht kehrt die Expedition nun ohne Antrieb zurück. Die Rückreise wird länger dauern als erwartet.',
            '7' => 'Deine Expedition hat die unendliche Leere des Weltraums kennengelernt. Es gab nicht einmal einen kleinen Asteroiden oder eine Strahlung oder ein Partikel, die diese Expedition interessant hätten machen können.',
            '8' => 'Nun, jetzt wissen wir, dass diese roten Anomalien der Klasse 5 nicht nur chaotische Auswirkungen auf die Navigationssysteme der Schiffe haben, sondern auch massive Halluzinationen bei der Besatzung auslösen. Die Expedition brachte nichts mit zurück.',
            '9' => 'Deine Expedition hat wunderschöne Bilder einer Supernova gemacht. Von der Expedition konnten keine neuen Erkenntnisse gewonnen werden, aber zumindest besteht eine gute Chance, den Wettbewerb "Bestes Bild des Universums" in der nächsten Ausgabe des OGame-Magazins zu gewinnen.',
            '10' => 'Deine Expeditionsflotte folgte eine Weile seltsamen Signalen. Am Ende stellte sich heraus, dass diese Signale von einer alten Sonde gesendet wurden, die vor Generationen ausgesandt wurde, um fremde Spezies zu begrüßen. Die Sonde wurde geborgen und einige Museen deines Heimatplaneten haben bereits ihr Interesse bekundet.',
            '11' => 'Trotz der anfänglich sehr vielversprechenden Scans dieses Sektors kehrten wir leider mit leeren Händen zurück.',
            '12' => 'Außer einigen sonderbaren, kleinen Haustieren von einem unbekannten Sumpfplaneten bringt diese Expedition nichts Aufregendes von der Reise zurück.',
            '13' => 'Das Flaggschiff der Expedition kollidierte mit einem fremden Schiff, das ohne Vorwarnung in die Flotte sprang. Das fremde Schiff explodierte und der Schaden am Flaggschiff war erheblich. Die Expedition kann unter diesen Umständen nicht fortgesetzt werden, daher wird die Flotte den Rückweg antreten, sobald die notwendigen Reparaturen durchgeführt wurden.',
            '14' => 'Unser Expeditionsteam stieß auf eine seltsame Kolonie, die vor Äonen verlassen wurde. Nach der Landung begann unsere Besatzung an hohem Fieber zu leiden, verursacht durch ein außerirdisches Virus. Es wurde festgestellt, dass dieses Virus die gesamte Zivilisation auf dem Planeten ausgelöscht hat. Unser Expeditionsteam ist auf dem Heimweg, um die erkrankten Besatzungsmitglieder zu behandeln. Leider mussten wir die Mission abbrechen und kehren mit leeren Händen zurück.',
            '15' => 'Ein seltsamer Computervirus griff das Navigationssystem kurz nach dem Verlassen unseres Heimatsystems an. Dies führte dazu, dass die Expeditionsflotte im Kreis flog. Unnötig zu sagen, dass die Expedition nicht wirklich erfolgreich war.',
        ],
    ],

    // Gain Resources
    'expedition_gain_resources' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Auf einem isolierten Planetoiden fanden wir einige leicht zugängliche Rohstofffelder und konnten erfolgreich Rohstoffe abbauen.',
            '2' => 'Deine Expedition entdeckte einen kleinen Asteroiden, von dem einige Rohstoffe abgebaut werden konnten.',
            '3' => 'Deine Expedition fand einen antiken, voll beladenen, aber verlassenen Frachterkonvoi. Einige der Rohstoffe konnten geborgen werden.',
            '4' => 'Deine Expeditionsflotte meldet die Entdeckung eines riesigen außerirdischen Schiffswracks. Sie konnten zwar nichts von deren Technologien lernen, aber das Schiff in seine Hauptbestandteile zerlegen und einige nützliche Rohstoffe daraus gewinnen.',
            '5' => 'Auf einem kleinen Mond mit eigener Atmosphäre fand deine Expedition riesige Rohstofflager. Die Mannschaft am Boden versucht, diesen natürlichen Schatz zu heben und zu verladen.',
            '6' => 'Mineraliengürtel um einen unbekannten Planeten enthielten unzählige Rohstoffe. Die Expeditionsschiffe kehren zurück und ihre Lager sind voll!',
        ],
    ],

    // Gain Dark Matter
    'expedition_gain_dark_matter' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Die Expedition folgte seltsamen Signalen zu einem Asteroiden. Im Kern des Asteroiden wurde eine kleine Menge Dunkler Materie gefunden. Der Asteroid wurde mitgenommen und die Forscher versuchen, die Dunkle Materie zu extrahieren.',
            '2' => 'Die Expedition konnte Dunkle Materie einfangen und speichern.',
            '3' => 'Wir trafen ein seltsames Alien auf der Ablage eines kleinen Schiffes, das uns im Tausch für einige einfache mathematische Berechnungen einen Koffer mit Dunkler Materie gab.',
            '4' => 'Wir fanden die Überreste eines außerirdischen Schiffes. Auf einem Regal im Frachtraum fanden wir einen kleinen Behälter mit etwas Dunkler Materie!',
            '5' => 'Unsere Expedition hatte Erstkontakt mit einer besonderen Rasse. Ein Wesen aus reiner Energie, das sich selbst Legorianer nannte, flog durch die Expeditionsschiffe und entschied sich dann, unserer unterentwickelten Spezies zu helfen. Ein Koffer mit Dunkler Materie materialisierte sich auf der Brücke des Schiffes!',
            '6' => 'Unsere Expedition übernahm ein Geisterschiff, das eine kleine Menge Dunkler Materie transportierte. Wir fanden keine Hinweise darauf, was mit der ursprünglichen Besatzung des Schiffes geschehen ist, aber unsere Techniker konnten die Dunkle Materie bergen.',
            '7' => 'Unsere Expedition vollbrachte ein einzigartiges Experiment. Sie konnten Dunkle Materie von einem sterbenden Stern ernten.',
            '8' => 'Unsere Expedition fand eine verrostete Raumstation, die scheinbar seit langer Zeit unkontrolliert durch den Weltraum schwebte. Die Station selbst war völlig nutzlos, allerdings wurde entdeckt, dass etwas Dunkle Materie im Reaktor gespeichert ist. Unsere Techniker versuchen, so viel wie möglich zu retten.',
        ],
    ],

    // Gain Ships
    'expedition_gain_ships' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Unsere Expedition fand einen Planeten, der durch eine Reihe von Kriegen fast zerstört wurde. In der Umlaufbahn schweben verschiedene Schiffe. Die Techniker versuchen, einige davon zu reparieren. Vielleicht erfahren wir auch, was hier geschehen ist.',
            '2' => 'Wir fanden eine verlassene Piratenstation. Im Hangar liegen einige alte Schiffe. Unsere Techniker prüfen, ob einige davon noch brauchbar sind.',
            '3' => 'Deine Expedition stieß auf die Werften einer Kolonie, die vor Äonen verlassen wurde. Im Hangar der Werften entdecken sie Schiffe, die geborgen werden können. Die Techniker versuchen, einige davon wieder flugfähig zu machen.',
            '4' => 'Wir stießen auf die Überreste einer früheren Expedition! Unsere Techniker werden versuchen, einige der Schiffe wieder zum Laufen zu bringen.',
            '5' => 'Unsere Expedition stieß auf eine alte automatische Werft. Einige der Schiffe befinden sich noch in der Produktionsphase und unsere Techniker versuchen derzeit, die Energiegeneratoren der Werft wieder zu aktivieren.',
            '6' => 'Wir fanden die Überreste einer Armada. Die Techniker machten sich direkt an die fast intakten Schiffe, um sie wieder zum Laufen zu bringen.',
            '7' => 'Wir fanden den Planeten einer ausgestorbenen Zivilisation. Wir können eine riesige intakte Raumstation sehen, die den Planeten umkreist. Einige deiner Techniker und Piloten gingen auf die Oberfläche, um nach Schiffen zu suchen, die noch verwendet werden können.',
        ],
    ],

    // Gain Item
    'expedition_gain_item' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Eine fliehende Flotte ließ einen Gegenstand zurück, um uns bei ihrer Flucht abzulenken.',
        ],
    ],

    // Failed and Speedup
    'expedition_failed_and_speedup' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Deine Expedition meldet keine Anomalien im erforschten Sektor. Aber die Flotte geriet auf dem Rückweg in Sonnenwind. Dadurch wurde die Rückreise beschleunigt. Deine Expedition kehrt etwas früher nach Hause zurück.',
            '2' => 'Der neue und wagemutige Kommandant reiste erfolgreich durch ein instabiles Wurmloch, um den Rückflug zu verkürzen! Die Expedition selbst brachte jedoch nichts Neues.',
            '3' => 'Eine unerwartete Rückkopplung in den Energiespulen der Triebwerke beschleunigte die Rückkehr der Expedition, sie kehrt früher als erwartet zurück. Ersten Berichten zufolge gibt es nichts Aufregendes zu vermelden.',
        ],
    ],

    // Failure and Delay
    'expedition_failed_and_delay' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Deine Expedition geriet in einen Sektor voller Partikelstürme. Dadurch wurden die Energiespeicher überladen und die meisten Hauptsysteme der Schiffe fielen aus. Deine Mechaniker konnten das Schlimmste verhindern, aber die Expedition wird mit großer Verspätung zurückkehren.',
            '2' => 'Dein Navigator machte einen schweren Fehler in seinen Berechnungen, wodurch der Sprung der Expedition falsch berechnet wurde. Die Flotte verfehlte nicht nur das Ziel komplett, sondern die Rückreise wird auch viel länger dauern als ursprünglich geplant.',
            '3' => 'Der Sonnenwind eines Roten Riesen ruinierte den Sprung der Expedition und es wird einige Zeit dauern, den Rücksprung zu berechnen. Außer der Leere des Weltraums zwischen den Sternen gab es in diesem Sektor nichts. Die Flotte wird später als erwartet zurückkehren.',
        ],
    ],

    // Battle
    'expedition_battle' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Einige primitive Barbaren greifen uns mit Raumschiffen an, die man kaum als solche bezeichnen kann. Wenn das Feuer ernst wird, werden wir gezwungen sein zurückzuschießen.',
            '2' => 'Wir mussten gegen einige Piraten kämpfen, die glücklicherweise nur wenige waren.',
            '3' => 'Wir haben Funkübertragungen von betrunkenen Piraten aufgefangen. Sieht so aus, als würden wir bald angegriffen werden.',
            '4' => 'Unsere Expedition wurde von einer kleinen Gruppe unbekannter Schiffe angegriffen!',
            '5' => 'Einige wirklich verzweifelte Weltraumpiraten versuchten, unsere Expeditionsflotte zu kapern.',
            '6' => 'Einige exotisch aussehende Schiffe griffen die Expeditionsflotte ohne Vorwarnung an!',
            '7' => 'Deine Expeditionsflotte hatte einen unfreundlichen Erstkontakt mit einer unbekannten Spezies.',
        ],
    ],

    // Battle - Pirates
    'expedition_battle_pirates' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Einige primitive Barbaren greifen uns mit Raumschiffen an, die man kaum als solche bezeichnen kann. Wenn das Feuer ernst wird, werden wir gezwungen sein zurückzuschießen.',
            '2' => 'Wir mussten gegen einige Piraten kämpfen, die glücklicherweise nur wenige waren.',
            '3' => 'Wir haben Funkübertragungen von betrunkenen Piraten aufgefangen. Sieht so aus, als würden wir bald angegriffen werden.',
            '4' => 'Unsere Expedition wurde von einer kleinen Gruppe Weltraumpiraten angegriffen!',
            '5' => 'Einige wirklich verzweifelte Weltraumpiraten versuchten, unsere Expeditionsflotte zu kapern.',
            '6' => 'Piraten haben die Expeditionsflotte ohne Vorwarnung überfallen!',
            '7' => 'Eine zusammengewürfelte Flotte von Weltraumpiraten hat uns abgefangen und Tribut gefordert.',
        ],
    ],

    // Battle - Aliens
    'expedition_battle_aliens' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Wir empfingen seltsame Signale von unbekannten Schiffen. Sie stellten sich als feindlich heraus!',
            '2' => 'Eine außerirdische Patrouille entdeckte unsere Expeditionsflotte und griff sofort an!',
            '3' => 'Deine Expeditionsflotte hatte einen unfreundlichen Erstkontakt mit einer unbekannten Spezies.',
            '4' => 'Einige exotisch aussehende Schiffe griffen die Expeditionsflotte ohne Vorwarnung an!',
            '5' => 'Eine Flotte außerirdischer Kriegsschiffe tauchte aus dem Hyperraum auf und griff uns an!',
            '6' => 'Wir trafen auf eine technologisch fortgeschrittene außerirdische Spezies, die nicht friedlich gesinnt war.',
            '7' => 'Unsere Sensoren erfassten unbekannte Energiesignaturen, bevor außerirdische Schiffe angriffen!',
        ],
    ],

    // Loss of Fleet
    'expedition_loss_of_fleet' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Eine Kernschmelze des Führungsschiffs führt zu einer Kettenreaktion, die die gesamte Expeditionsflotte in einer spektakulären Explosion zerstört.',
        ],
    ],

    // Merchant Found
    'expedition_merchant_found' => [
        'from' => 'Flottenkommando',
        'subject' => 'Expeditionsergebnis',
        'body' => [
            '1' => 'Deine Expeditionsflotte hat Kontakt mit einer freundlichen außerirdischen Rasse aufgenommen. Sie kündigten an, einen Vertreter mit Handelswaren zu deinen Welten zu senden.',
            '2' => 'Ein geheimnisvolles Handelsschiff näherte sich deiner Expedition. Der Händler bot an, deine Planeten zu besuchen und spezielle Handelsdienste anzubieten.',
            '3' => 'Die Expedition traf auf einen intergalaktischen Händlerkonvoi. Einer der Händler hat zugestimmt, deine Heimatwelt zu besuchen, um Handelsmöglichkeiten anzubieten.',
        ],
    ],

    // ------------------------
    // Buddy Request Received
    'buddy_request_received' => [
        'from' => 'Buddies',
        'subject' => 'Buddyanfrage',
        'body' => 'Du hast eine neue Buddyanfrage von :sender_name erhalten.<span style="display:none;">:buddy_request_id</span>',
    ],

    // ------------------------
    // Buddy Request Accepted
    'buddy_request_accepted' => [
        'from' => 'Buddies',
        'subject' => 'Buddyanfrage angenommen',
        'body' => 'Spieler :accepter_name hat dich zu seiner Buddyliste hinzugefügt.',
    ],

    // ------------------------
    // Buddy Removed
    'buddy_removed' => [
        'from' => 'Buddies',
        'subject' => 'Du wurdest von einer Buddyliste entfernt',
        'body' => 'Spieler :remover_name hat dich von seiner Buddyliste entfernt.',
    ],

    // ------------------------
    // Missile Attack Report (Attacker)
    'missile_attack_report' => [
        'from' => 'Flottenkommando',
        'subject' => 'Raketenangriff auf :target_coords',
        'body' => 'Deine Interplanetarraketen von :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) haben ihr Ziel bei :target_planet_name :target_coords (ID: :target_planet_id, Typ: :target_type) erreicht.

Abgeschossene Raketen: :missiles_sent
Abgefangene Raketen: :missiles_intercepted
Eingeschlagene Raketen: :missiles_hit

Zerstörte Verteidigungsanlagen: :defenses_destroyed',
        // Sub-keys used by MissileAttackReport::getBody()
        'missile_singular'   => 'Rakete',
        'missile_plural'     => 'Raketen',
        'from_your_planet'   => ' von deinem Planeten ',
        'smashed_into'       => ' schlugen auf dem Planeten ein ',
        'intercepted_label'  => 'Abgefangene Raketen:',
        'defenses_hit_label' => 'Getroffene Verteidigungsanlagen',
        'none'               => 'Keine',
    ],

    // ------------------------
    // Missile Defense Report (Defender)
    'missile_defense_report' => [
        'from' => 'Verteidigungskommando',
        'subject' => 'Raketenangriff auf :planet_coords',
        'body' => 'Dein Planet :planet_name bei :planet_coords (ID: :planet_id) wurde von Interplanetarraketen von :attacker_name angegriffen!

Eingehende Raketen: :missiles_incoming
Abgefangene Raketen: :missiles_intercepted
Eingeschlagene Raketen: :missiles_hit

Zerstörte Verteidigungsanlagen: :defenses_destroyed',
        // Sub-keys used by MissileDefenseReport::getBody()
        'your_planet'        => 'Dein Planet ',
        'attacked_by_prefix' => ' wurde von Interplanetarraketen angegriffen von ',
        'incoming_label'     => 'Eingehende Raketen:',
        'intercepted_label'  => 'Abgefangene Raketen:',
        'defenses_hit_label' => 'Getroffene Verteidigungsanlagen',
        'none'               => 'Keine',
    ],

    // ------------------------
    // Alliance Broadcast
    'alliance_broadcast' => [
        'from' => ':sender_name',
        'subject' => '[:alliance_tag] Allianzrundschreiben von :sender_name',
        'body' => ':message',
    ],

    // ------------------------
    // Alliance Application Received
    'alliance_application_received' => [
        'from' => 'Allianzverwaltung',
        'subject' => 'Neue Allianzbewerbung',
        'body' => 'Spieler :applicant_name hat sich bei deiner Allianz beworben.

Bewerbungsnachricht:
:application_message',
    ],

    // Planet relocation messages
    'planet_relocation_success' => [
        'from' => 'Kolonien verwalten',
        'subject' => 'Umsiedlung von :planet_name war erfolgreich',
        'body' => 'Der Planet :planet_name wurde erfolgreich von den Koordinaten [coordinates]:old_coordinates[/coordinates] nach [coordinates]:new_coordinates[/coordinates] umgesiedelt.',
    ],

    // Fleet union invite
    'fleet_union_invite' => [
        'from' => 'Flottenkommando',
        'subject' => 'Einladung zum Allianzkampf',
        'body' => ':sender_name hat dich zur Mission :union_name gegen :target_player auf [:target_coords] eingeladen, die Flotte wurde auf :arrival_time getimt.

ACHTUNG: Die Ankunftszeit kann sich durch beitretende Flotten ändern. Jede neue Flotte kann diese Zeit um maximal 30% verlängern, andernfalls wird sie nicht zum Beitritt zugelassen.

HINWEIS: Die Gesamtstärke aller Teilnehmer im Vergleich zur Gesamtstärke der Verteidiger bestimmt, ob es ein ehrenvoller Kampf wird oder nicht.',
    ],

    // Building upgrade messages
    'Shipyard is being upgraded.' => 'Die Raumschiffwerft wird ausgebaut.',
    'Nanite Factory is being upgraded.' => 'Die Nanitenfabrik wird ausgebaut.',

    // ------------------------
    // Moon destruction messages (attacker)
    // TODO: these moon destruction messages are not correct and should be updated with
    // real official messages from the original game. These are just placeholders for now.
    'moon_destruction_success' => [
        'from' => 'Flottenkommando',
        'subject' => 'Mond :moon_name [:moon_coords] wurde zerstört!',
        'body' => 'Mit einer Zerstörungswahrscheinlichkeit von :destruction_chance und einer Todesstern-Verlustwahrscheinlichkeit von :loss_chance hat deine Flotte den Mond :moon_name bei :moon_coords erfolgreich zerstört.',
    ],

    // ------------------------
    'moon_destruction_failure' => [
        'from' => 'Flottenkommando',
        'subject' => 'Mondzerstörung bei :moon_coords fehlgeschlagen',
        'body' => 'Mit einer Zerstörungswahrscheinlichkeit von :destruction_chance und einer Todesstern-Verlustwahrscheinlichkeit von :loss_chance konnte deine Flotte den Mond :moon_name bei :moon_coords nicht zerstören. Die Flotte kehrt zurück.',
    ],

    // ------------------------
    'moon_destruction_catastrophic' => [
        'from' => 'Flottenkommando',
        'subject' => 'Katastrophaler Verlust bei Mondzerstörung bei :moon_coords',
        'body' => 'Mit einer Zerstörungswahrscheinlichkeit von :destruction_chance und einer Todesstern-Verlustwahrscheinlichkeit von :loss_chance konnte deine Flotte den Mond :moon_name bei :moon_coords nicht zerstören. Zusätzlich gingen alle Todessterne bei dem Versuch verloren. Es gibt kein Wrackfeld.',
    ],

    // ------------------------
    'moon_destruction_mission_failed' => [
        'from' => 'Flottenkommando',
        'subject' => 'Mondzerstörungsmission bei :coordinates fehlgeschlagen',
        'body' => 'Deine Flotte ist bei :coordinates angekommen, aber es wurde kein Mond am Zielort gefunden. Die Flotte kehrt zurück.',
    ],

    // ------------------------
    // Moon destruction messages (defender)
    'moon_destruction_repelled' => [
        'from' => 'Weltraumüberwachung',
        'subject' => 'Zerstörungsversuch auf Mond :moon_name [:moon_coords] abgewehrt',
        'body' => ':attacker_name hat deinen Mond :moon_name bei :moon_coords mit einer Zerstörungswahrscheinlichkeit von :destruction_chance und einer Todesstern-Verlustwahrscheinlichkeit von :loss_chance angegriffen. Dein Mond hat den Angriff überlebt!',
    ],

    // ------------------------
    'moon_destroyed' => [
        'from' => 'Weltraumüberwachung',
        'subject' => 'Mond :moon_name [:moon_coords] wurde zerstört!',
        'body' => 'Dein Mond :moon_name bei :moon_coords wurde von einer Todessternflotte von :attacker_name zerstört!',
    ],

    // ------------------------
    // Wreck field repair completed
    'wreck_field_repair_completed' => [
        'from' => 'Systemnachricht',
        'subject' => 'Reparatur abgeschlossen',
        'body' => 'Dein Reparaturauftrag auf Planet :planet wurde abgeschlossen.
:ship_count Schiffe wurden wieder in Dienst gestellt.',
    ],
];
