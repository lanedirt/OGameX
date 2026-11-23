<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outgame / Landing page - Italiano
    |--------------------------------------------------------------------------
    */

    // Avviso browser obsoleto
    'browser_warning' => [
        'title'  => 'Il tuo browser non è aggiornato.',
        'desc1'  => 'La versione di Internet Explorer in uso non è conforme agli standard attuali e non è più supportata da questo sito.',
        'desc2'  => 'Per utilizzare questo sito, aggiorna il browser a una versione recente oppure usa un browser diverso. Se stai già usando la versione più recente, ricarica la pagina per visualizzarla correttamente.',
        'desc3'  => 'Ecco un elenco dei browser più diffusi. Fai clic su uno dei simboli per accedere alla pagina di download:',
    ],

    // Form di accesso (intestazione)
    'login' => [
        'page_title'        => 'OGame - Conquista l\'universo',
        'btn'               => 'Accedi',
        'email_label'       => 'Indirizzo e-mail:',
        'password_label'    => 'Password:',
        'universe_label'    => 'Universo:',
        'universe_option_1' => '1. Universo',
        'submit'            => 'Accedi',
        'forgot_password'   => 'Hai dimenticato la password?',
        'forgot_email'      => 'Hai dimenticato l\'indirizzo e-mail?',
        'terms_accept_html' => 'Accedendo accetto i <a class="" href="#" target="_blank" title="T&amp;C">T&amp;C</a>',
    ],

    // Form di registrazione (colonna laterale)
    'register' => [
        'play_free'      => 'GIOCA GRATIS!',
        'email_label'    => 'Indirizzo e-mail:',
        'password_label' => 'Password:',
        'universe_label' => 'Universo:',
        'distinctions'   => 'Caratteristiche',
        'terms_html'     => 'Nel gioco si applicano i nostri <a class="" target="_blank" href="#" title="T&amp;C"> T&amp;C </a> e la <a class="" target="_blank" href="#" title="Informativa Privacy"> Informativa Privacy </a>',
        'submit'         => 'Registrati',
    ],

    // Schede di navigazione superiori
    'nav' => [
        'home'  => 'Home',
        'about' => 'Su OGame',
        'media' => 'Media',
        'wiki'  => 'Wiki',
    ],

    // Contenuto scheda Home
    'home' => [
        'title'            => 'OGame - Conquista l\'universo',
        'description_html' => '<em>OGame</em> è un gioco di strategia ambientato nello spazio, con migliaia di giocatori da tutto il mondo che si sfidano contemporaneamente. Ti basta un normale browser web per giocare.',
        'board_btn'        => 'Forum',
        'trailer_title'    => 'Trailer',
    ],

    // Piè di pagina
    'footer' => [
        'legal'          => 'Note Legali',
        'privacy_policy' => 'Informativa Privacy',
        'terms'          => 'T&C',
        'contact'        => 'Contatti',
        'rules'          => 'Regole',
        'copyright'      => '© OGameX. Tutti i diritti riservati.',
    ],

    // Stringhe JS inline
    'js' => [
        'login'            => 'Accedi',
        'close'            => 'Chiudi',
        'age_check_failed' => 'Siamo spiacenti, non sei idoneo alla registrazione. Consulta i T&C per maggiori informazioni.',
    ],

    // Stringhe jQuery ValidationEngine
    'validation' => [
        'required'                   => 'Questo campo è obbligatorio',
        'make_decision'              => 'Effettua una scelta',
        'accept_terms'               => 'Devi accettare i Termini e Condizioni.',
        'length'                     => 'Sono consentiti da 3 a 20 caratteri.',
        'pw_length'                  => 'Sono consentiti da 4 a 20 caratteri.',
        'email'                      => 'Inserisci un indirizzo e-mail valido!',
        'invalid_chars'              => 'Contiene caratteri non validi.',
        'no_begin_end_underscore'    => 'Il nome non può iniziare o terminare con un trattino basso.',
        'no_begin_end_whitespace'    => 'Il nome non può iniziare o terminare con uno spazio.',
        'max_three_underscores'      => 'Il nome non può contenere più di 3 trattini bassi in totale.',
        'max_three_whitespaces'      => 'Il nome non può contenere più di 3 spazi in totale.',
        'no_consecutive_underscores' => 'Non puoi usare due o più trattini bassi consecutivi.',
        'no_consecutive_whitespaces' => 'Non puoi usare due o più spazi consecutivi.',
        'username_available'         => 'Questo nome utente è disponibile.',
        'username_loading'           => 'Attendere, caricamento in corso...',
        'username_taken'             => 'Questo nome utente non è più disponibile.',
        'only_letters'               => 'Usa solo lettere.',
    ],

    // Pagina password dimenticata
    'forgot_password' => [
        'title'          => 'Hai dimenticato la password?',
        'description'    => 'Inserisci il tuo indirizzo e-mail e ti invieremo un link per reimpostare la password.',
        'email_label'    => 'Indirizzo e-mail:',
        'submit'         => 'Invia link di ripristino',
        'back_to_login'  => '← Torna al login',
    ],

    // Pagina reimposta password
    'reset_password' => [
        'title'          => 'Reimposta la password',
        'email_label'    => 'Indirizzo e-mail:',
        'password_label' => 'Nuova password:',
        'confirm_label'  => 'Conferma nuova password:',
        'submit'         => 'Reimposta password',
    ],

    // Pagina e-mail dimenticata
    'forgot_email' => [
        'title'          => 'Hai dimenticato l\'indirizzo e-mail?',
        'description'    => 'Inserisci il nome del tuo comandante e ti invieremo un suggerimento all\'indirizzo e-mail registrato.',
        'username_label' => 'Nome comandante:',
        'submit'         => 'Invia suggerimento',
        'back_to_login'  => '← Torna al login',
        'sent'           => 'Se è stato trovato un account corrispondente, è stato inviato un suggerimento all\'indirizzo e-mail registrato.',
    ],

    // Template e-mail in uscita
    'mail' => [
        'reset_password' => [
            'subject'      => 'Reimposta la tua password OGameX',
            'heading'      => 'Ripristino Password',
            'greeting'     => 'Ciao :username,',
            'body'         => 'Abbiamo ricevuto una richiesta per reimpostare la password del tuo account. Clicca il pulsante qui sotto per scegliere una nuova password.',
            'cta'          => 'Reimposta Password',
            'expiry'       => 'Questo link scadrà tra 60 minuti.',
            'no_action'    => 'Se non hai richiesto il ripristino della password, non è necessaria alcuna ulteriore azione.',
            'url_fallback' => 'Se hai problemi a cliccare il pulsante, copia e incolla l\'URL qui sotto nel tuo browser:',
        ],
        'retrieve_email' => [
            'subject'   => 'Il tuo indirizzo e-mail OGameX',
            'heading'   => 'Suggerimento Indirizzo E-mail',
            'greeting'  => 'Ciao :username,',
            'body'      => 'Hai richiesto un suggerimento per l\'indirizzo e-mail associato al tuo account:',
            'cta'       => 'Vai al Login',
            'no_action' => 'Se non hai effettuato questa richiesta, puoi ignorare questa e-mail.',
        ],
    ],

    // Testi tooltip caratteristiche universo
    'universe_characteristics' => [
        'fleet_speed'      => 'Velocità flotta: maggiore è il valore, meno tempo hai per reagire a un attacco.',
        'economy_speed'    => 'Velocità economia: maggiore è il valore, più veloci saranno costruzioni, ricerche e raccolta risorse.',
        'debris_ships'     => 'Alcune navi distrutte in battaglia finiranno nel campo di detriti.',
        'debris_defence'   => 'Alcune strutture difensive distrutte in battaglia finiranno nel campo di detriti.',
        'dark_matter_gift' => 'Riceverai Materia Oscura come ricompensa per la conferma dell\'indirizzo e-mail.',
        'aks_on'           => 'Sistema di battaglia alleanza attivato',
        'planet_fields'    => 'Il numero massimo di slot di costruzione è stato aumentato.',
        'wreckfield'       => 'Cantiere Spaziale attivato: alcune navi distrutte possono essere ripristinate tramite il Cantiere Spaziale.',
        'universe_big'     => 'Numero di Galassie nell\'Universo',
    ],
];
