<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outgame / Landing page - Nederlands
    |--------------------------------------------------------------------------
    */

    // Browser verouderd waarschuwing
    'browser_warning' => [
        'title'  => 'Uw browser is niet up-to-date.',
        'desc1'  => 'Uw versie van Internet Explorer voldoet niet aan de huidige standaarden en wordt niet langer ondersteund door deze website.',
        'desc2'  => 'Om deze website te gebruiken, update uw browser naar een actuele versie of gebruik een andere browser. Als u al de nieuwste versie gebruikt, ververs dan de pagina om deze correct te kunnen bekijken.',
        'desc3'  => 'Hier is een lijst van de populairste browsers. Klik op een van de symbolen om naar de downloadpagina te gaan:',
    ],

    // Inlogformulier (header)
    'login' => [
        'page_title'        => 'OGame - Verover het universum',
        'btn'               => 'Inloggen',
        'email_label'       => 'E-mailadres:',
        'password_label'    => 'Wachtwoord:',
        'universe_label'    => 'Universum:',
        'universe_option_1' => '1. Universum',
        'submit'            => 'Inloggen',
        'forgot_password'   => 'Wachtwoord vergeten?',
        'forgot_email'      => 'E-mailadres vergeten?',
        'terms_accept_html' => 'Door in te loggen accepteer ik de <a class="" href="#" target="_blank" title="Gebruiksvoorwaarden">Gebruiksvoorwaarden</a>',
    ],

    // Registratieformulier (zijbalk)
    'register' => [
        'play_free'      => 'SPEEL GRATIS!',
        'email_label'    => 'E-mailadres:',
        'password_label' => 'Wachtwoord:',
        'universe_label' => 'Universum:',
        'distinctions'   => 'Kenmerken',
        'terms_html'     => 'Onze <a class="" target="_blank" href="#" title="Gebruiksvoorwaarden"> Gebruiksvoorwaarden </a> en <a class="" target="_blank" href="#" title="Privacybeleid"> Privacybeleid </a> zijn van toepassing in het spel',
        'submit'         => 'Registreren',
    ],

    // Navigatietabbladen bovenaan
    'nav' => [
        'home'  => 'Home',
        'about' => 'Over OGame',
        'media' => 'Media',
        'wiki'  => 'Wiki',
    ],

    // Inhoud tabblad Home
    'home' => [
        'title'            => 'OGame - Verover het universum',
        'description_html' => '<em>OGame</em> is een strategiespel in de ruimte, waarbij duizenden spelers van over de hele wereld tegelijkertijd strijden. U heeft alleen een gewone webbrowser nodig om te spelen.',
        'board_btn'        => 'Forum',
        'trailer_title'    => 'Trailer',
    ],

    // Voettekst
    'footer' => [
        'legal'          => 'Juridisch',
        'privacy_policy' => 'Privacybeleid',
        'terms'          => 'Gebruiksvoorwaarden',
        'contact'        => 'Contact',
        'rules'          => 'Regels',
        'copyright'      => '© OGameX. Alle rechten voorbehouden.',
    ],

    // Inline JS-strings
    'js' => [
        'login'            => 'Inloggen',
        'close'            => 'Sluiten',
        'age_check_failed' => 'Het spijt ons, maar u komt niet in aanmerking voor registratie. Raadpleeg onze gebruiksvoorwaarden voor meer informatie.',
    ],

    // jQuery ValidationEngine strings
    'validation' => [
        'required'                   => 'Dit veld is verplicht',
        'make_decision'              => 'Maak een keuze',
        'accept_terms'               => 'U moet de gebruiksvoorwaarden accepteren.',
        'length'                     => 'Tussen 3 en 20 tekens toegestaan.',
        'pw_length'                  => 'Tussen 4 en 20 tekens toegestaan.',
        'email'                      => 'Voer een geldig e-mailadres in!',
        'invalid_chars'              => 'Bevat ongeldige tekens.',
        'no_begin_end_underscore'    => 'Uw naam mag niet beginnen of eindigen met een onderstrepingsteken.',
        'no_begin_end_whitespace'    => 'Uw naam mag niet beginnen of eindigen met een spatie.',
        'max_three_underscores'      => 'Uw naam mag niet meer dan 3 onderstrepingstekens bevatten.',
        'max_three_whitespaces'      => 'Uw naam mag niet meer dan 3 spaties bevatten.',
        'no_consecutive_underscores' => 'U mag niet twee of meer aaneengesloten onderstrepingstekens gebruiken.',
        'no_consecutive_whitespaces' => 'U mag niet twee of meer aaneengesloten spaties gebruiken.',
        'username_available'         => 'Deze gebruikersnaam is beschikbaar.',
        'username_loading'           => 'Even geduld, aan het laden...',
        'username_taken'             => 'Deze gebruikersnaam is niet meer beschikbaar.',
        'only_letters'               => 'Gebruik alleen letters.',
    ],

    // Pagina wachtwoord vergeten
    'forgot_password' => [
        'title'          => 'Wachtwoord vergeten?',
        'description'    => 'Voer uw e-mailadres in en wij sturen u een link om uw wachtwoord opnieuw in te stellen.',
        'email_label'    => 'E-mailadres:',
        'submit'         => 'Resetlink versturen',
        'back_to_login'  => '← Terug naar inloggen',
    ],

    // Pagina wachtwoord opnieuw instellen
    'reset_password' => [
        'title'          => 'Wachtwoord opnieuw instellen',
        'email_label'    => 'E-mailadres:',
        'password_label' => 'Nieuw wachtwoord:',
        'confirm_label'  => 'Nieuw wachtwoord bevestigen:',
        'submit'         => 'Wachtwoord opnieuw instellen',
    ],

    // Pagina e-mailadres vergeten
    'forgot_email' => [
        'title'          => 'E-mailadres vergeten?',
        'description'    => 'Voer uw commandantsnaam in en wij sturen een hint naar het geregistreerde e-mailadres.',
        'username_label' => 'Commandantsnaam:',
        'submit'         => 'Hint versturen',
        'back_to_login'  => '← Terug naar inloggen',
        'sent'           => 'Als er een overeenkomend account is gevonden, is er een hint naar het geregistreerde e-mailadres gestuurd.',
    ],

    // Uitgaande e-mailsjablonen
    'mail' => [
        'reset_password' => [
            'subject'      => 'Stel uw OGameX-wachtwoord opnieuw in',
            'heading'      => 'Wachtwoord Opnieuw Instellen',
            'greeting'     => 'Hallo :username,',
            'body'         => 'We hebben een verzoek ontvangen om het wachtwoord voor uw account opnieuw in te stellen. Klik op de knop hieronder om een nieuw wachtwoord te kiezen.',
            'cta'          => 'Wachtwoord Opnieuw Instellen',
            'expiry'       => 'Deze link verloopt over 60 minuten.',
            'no_action'    => 'Als u geen wachtwoordreset heeft aangevraagd, is geen verdere actie vereist.',
            'url_fallback' => 'Als u problemen heeft met het klikken op de knop, kopieer en plak dan de onderstaande URL in uw browser:',
        ],
        'retrieve_email' => [
            'subject'   => 'Uw OGameX e-mailadres',
            'heading'   => 'Hint E-mailadres',
            'greeting'  => 'Hallo :username,',
            'body'      => 'U heeft een hint aangevraagd voor het e-mailadres dat aan uw account is gekoppeld:',
            'cta'       => 'Ga naar Inloggen',
            'no_action' => 'Als u dit verzoek niet heeft gedaan, kunt u deze e-mail veilig negeren.',
        ],
    ],

    // Tooltip-teksten universum kenmerken
    'universe_characteristics' => [
        'fleet_speed'      => 'Vlootsnelheid: hoe hoger de waarde, hoe minder tijd u heeft om te reageren op een aanval.',
        'economy_speed'    => 'Economische snelheid: hoe hoger de waarde, hoe sneller bouwwerken en onderzoeken worden voltooid en resources worden verzameld.',
        'debris_ships'     => 'Sommige schepen die in gevecht vernietigd worden, belanden in het puinveld.',
        'debris_defence'   => 'Sommige verdedigingsstructuren die in gevecht vernietigd worden, belanden in het puinveld.',
        'dark_matter_gift' => 'U ontvangt Donkere Materie als beloning voor het bevestigen van uw e-mailadres.',
        'aks_on'           => 'Alliantiegevechtsysteem geactiveerd',
        'planet_fields'    => 'Het maximale aantal bouwplaatsen is verhoogd.',
        'wreckfield'       => 'Ruimtedok geactiveerd: sommige vernietigde schepen kunnen worden hersteld met het Ruimtedok.',
        'universe_big'     => 'Aantal sterrenstelsels in het Universum',
    ],
];
