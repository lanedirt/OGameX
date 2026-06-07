<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outgame / Landing page - German
    |--------------------------------------------------------------------------
    */

    // Browser outdated warning
    'browser_warning' => [
        'title'  => 'Dein Browser ist nicht aktuell.',
        'desc1'  => 'Deine Internet Explorer Version entspricht nicht den aktuellen Standards und wird von dieser Webseite nicht mehr unterstützt.',
        'desc2'  => 'Um diese Webseite zu nutzen, aktualisiere bitte deinen Webbrowser auf eine aktuelle Version oder verwende einen anderen Webbrowser. Falls du bereits die neueste Version verwendest, lade die Seite bitte neu, um sie korrekt anzuzeigen.',
        'desc3'  => "Hier ist eine Liste der beliebtesten Browser. Klicke auf eines der Symbole, um zur Downloadseite zu gelangen:",
    ],

    // Login form (header)
    'login' => [
        'page_title'        => 'OGame - Erobere das Universum',
        'btn'               => 'Anmelden',
        'email_label'       => 'E-Mail-Adresse:',
        'password_label'    => 'Passwort:',
        'universe_label'    => 'Universum:',
        'universe_option_1' => '1. Universum',
        'submit'            => 'Anmelden',
        'forgot_password'   => 'Passwort vergessen?',
        'forgot_email'      => 'E-Mail-Adresse vergessen?',
        'terms_accept_html' => 'Mit der Anmeldung akzeptiere ich die <a class="" href="#" target="_blank" title="AGB">AGB</a>',
    ],

    // Registration form (sidebar)
    'register' => [
        'play_free'    => 'JETZT KOSTENLOS SPIELEN!',
        'email_label'  => 'E-Mail-Adresse:',
        'password_label' => 'Passwort:',
        'universe_label' => 'Universum:',
        'distinctions' => 'Auszeichnungen',
        'terms_html'   => 'Es gelten unsere <a class="" target="_blank" href="#" title="AGB"> AGB </a> und <a class="" target="_blank" href="#" title="Datenschutzrichtlinie"> Datenschutzrichtlinie </a> im Spiel',
        'submit'       => 'Registrieren',
    ],

    // Top navigation tabs
    'nav' => [
        'home'  => 'Startseite',
        'about' => 'Über OGame',
        'media' => 'Medien',
        'wiki'  => 'Wiki',
    ],

    // Home tab content
    'home' => [
        'title'            => 'OGame - Erobere das Universum',
        'description_html' => '<em>OGame</em> ist ein Strategiespiel im Weltraum, bei dem Tausende von Spielern aus der ganzen Welt gleichzeitig gegeneinander antreten. Du benötigst nur einen normalen Webbrowser zum Spielen.',
        'board_btn'        => 'Forum',
        'trailer_title'    => 'Trailer',
    ],

    // Footer
    'footer' => [
        'legal'          => 'Impressum',
        'privacy_policy' => 'Datenschutzrichtlinie',
        'terms'          => 'AGB',
        'contact'        => 'Kontakt',
        'rules'          => 'Regeln',
        'copyright'      => '© OGameX. Alle Rechte vorbehalten.',
    ],

    // Inline JS strings
    'js' => [
        'login'            => 'Anmelden',
        'close'            => 'Schließen',
        'age_check_failed' => 'Es tut uns leid, aber du bist nicht berechtigt, dich zu registrieren. Bitte sieh dir unsere AGB für weitere Informationen an.',
    ],

    // jQuery ValidationEngine strings
    'validation' => [
        'required'                  => 'Dieses Feld ist erforderlich',
        'make_decision'             => 'Triff eine Auswahl',
        'accept_terms'              => 'Du musst die AGB akzeptieren.',
        'length'                    => 'Zwischen 3 und 20 Zeichen erlaubt.',
        'pw_length'                 => 'Zwischen 4 und 20 Zeichen erlaubt.',
        'email'                     => 'Du musst eine gültige E-Mail-Adresse eingeben!',
        'invalid_chars'             => 'Enthält ungültige Zeichen.',
        'no_begin_end_underscore'   => 'Dein Name darf nicht mit einem Unterstrich beginnen oder enden.',
        'no_begin_end_whitespace'   => 'Dein Name darf nicht mit einem Leerzeichen beginnen oder enden.',
        'max_three_underscores'     => 'Dein Name darf insgesamt nicht mehr als 3 Unterstriche enthalten.',
        'max_three_whitespaces'     => 'Dein Name darf insgesamt nicht mehr als 3 Leerzeichen enthalten.',
        'no_consecutive_underscores' => 'Du darfst nicht zwei oder mehr Unterstriche hintereinander verwenden.',
        'no_consecutive_whitespaces' => 'Du darfst nicht zwei oder mehr Leerzeichen hintereinander verwenden.',
        'username_available'        => 'Dieser Benutzername ist verfügbar.',
        'username_loading'          => 'Bitte warten, wird geladen...',
        'username_taken'            => 'Dieser Benutzername ist nicht mehr verfügbar.',
        'only_letters'              => 'Verwende nur Buchstaben.',
    ],

    // Universe selection characteristics tooltip texts
    'universe_characteristics' => [
        'fleet_speed'     => 'Flottengeschwindigkeit: Je höher der Wert, desto weniger Zeit bleibt dir, auf einen Angriff zu reagieren.',
        'economy_speed'   => 'Wirtschaftsgeschwindigkeit: Je höher der Wert, desto schneller werden Gebäude und Forschungen fertiggestellt und Rohstoffe gesammelt.',
        'debris_ships'    => 'Ein Teil der in der Schlacht zerstörten Schiffe geht in das Trümmerfeld ein.',
        'debris_defence'  => 'Ein Teil der in der Schlacht zerstörten Verteidigungsanlagen geht in das Trümmerfeld ein.',
        'dark_matter_gift' => 'Du erhältst Dunkle Materie als Belohnung für die Bestätigung deiner E-Mail-Adresse.',
        'aks_on'          => 'Allianzkampfsystem aktiviert',
        'planet_fields'   => 'Die maximale Anzahl an Bauplätzen wurde erhöht.',
        'wreckfield'      => 'Raumdock aktiviert: Einige zerstörte Schiffe können mithilfe des Raumdocks wiederhergestellt werden.',
        'universe_big'    => 'Anzahl der Galaxien im Universum',
    ],
];
