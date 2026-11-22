<?php

return [
    // Messaggi di errore
    'error' => [
        'cannot_send_to_self' => 'Non puoi inviare una richiesta di amicizia a te stesso.',
        'user_not_found' => 'Utente non trovato.',
        'cannot_send_to_admin' => 'Non puoi inviare richieste di amicizia agli amministratori.',
        'cannot_send_to_user' => 'Non puoi inviare una richiesta di amicizia a questo utente.',
        'already_buddies' => 'Sei già amico di questo utente.',
        'request_exists' => 'Esiste già una richiesta di amicizia tra questi utenti.',
        'request_not_found' => 'Richiesta di amicizia non trovata.',
        'not_authorized_accept' => 'Non sei autorizzato ad accettare questa richiesta.',
        'not_authorized_reject' => 'Non sei autorizzato a rifiutare questa richiesta.',
        'not_authorized_cancel' => 'Non sei autorizzato ad annullare questa richiesta.',
        'already_processed' => 'Questa richiesta è già stata elaborata.',
        'relationship_not_found' => 'Relazione di amicizia non trovata.',
        'cannot_ignore_self' => 'Non puoi ignorare te stesso.',
        'already_ignored' => 'Il giocatore è già ignorato.',
        'not_in_ignore_list' => 'Il giocatore non è nella tua lista ignorati.',
        'send_request_failed' => 'Invio della richiesta di amicizia fallito.',
        'ignore_player_failed' => 'Impossibile ignorare il giocatore.',
        'delete_buddy_failed' => 'Eliminazione dell\'amico fallita',
        'search_too_short' => 'Troppo pochi caratteri! Inserisci almeno 2 caratteri.',
        'invalid_action' => 'Azione non valida',
    ],

    // Messaggi di successo
    'success' => [
        'request_sent' => 'Richiesta di amicizia inviata con successo!',
        'request_cancelled' => 'Richiesta di amicizia annullata con successo.',
        'request_accepted' => 'Richiesta di amicizia accettata!',
        'request_rejected' => 'Richiesta di amicizia rifiutata',
        'request_accepted_symbol' => '✓ Richiesta di amicizia accettata',
        'request_rejected_symbol' => '✗ Richiesta di amicizia rifiutata',
        'buddy_deleted' => 'Amico eliminato con successo!',
        'player_ignored' => 'Giocatore ignorato con successo!',
        'player_unignored' => 'Giocatore rimosso dalla lista ignorati.',
    ],

    // Etichette e titoli dell'interfaccia
    'ui' => [
        'page_title' => 'Amici',
        'my_buddies' => 'I miei amici',
        'ignored_players' => 'Giocatori ignorati',
        'buddy_request' => 'richiesta di amicizia',
        'buddy_request_title' => 'Richiesta di amicizia',
        'buddy_request_to' => 'Richiesta di amicizia a',
        'buddy_requests' => 'Richieste di amicizia',
        'new_buddy_request' => 'Nuova richiesta di amicizia',
        'write_message' => 'Scrivi messaggio',
        'send_message' => 'Invia messaggio',
        'send' => 'invia',
        'search_placeholder' => 'Cerca...',
        'no_buddies_found' => 'Nessun amico trovato',
        'no_buddy_requests' => 'Al momento non hai richieste di amicizia.',
        'no_requests_sent' => 'Non hai inviato nessuna richiesta di amicizia.',
        'no_ignored_players' => 'Nessun giocatore ignorato',
        'requests_received' => 'richieste ricevute',
        'requests_sent' => 'richieste inviate',
        'new' => 'nuove',
        'new_label' => 'Nuova',
        'from' => 'Da:',
        'to' => 'A:',
        'online' => 'online',
        'status_on' => 'On',
        'status_off' => 'Off',
        'received_request_from' => 'Hai ricevuto una nuova richiesta di amicizia da',
        'buddy_request_to_player' => 'Richiesta di amicizia al giocatore',
        'ignore_player_title' => 'Ignora giocatore',
    ],

    // Azioni
    'action' => [
        'accept_request' => 'Accetta richiesta di amicizia',
        'reject_request' => 'Rifiuta richiesta di amicizia',
        'withdraw_request' => 'Ritira richiesta di amicizia',
        'delete_buddy' => 'Elimina amico',
        'confirm_delete_buddy' => 'Vuoi davvero eliminare il tuo amico',
        'add_as_buddy' => 'Aggiungi come amico',
        'ignore_player' => 'Sei sicuro di voler ignorare',
        'remove_from_ignore' => 'Rimuovi dalla lista ignorati',
        'report_message' => 'Segnalare questo messaggio a un operatore di gioco?',
    ],

    // Intestazioni tabella
    'table' => [
        'id' => 'ID',
        'name' => 'Nome',
        'points' => 'Punti',
        'rank' => 'Classifica',
        'alliance' => 'Alleanza',
        'coords' => 'Coordinate',
        'actions' => 'Azioni',
    ],

    // Comuni
    'common' => [
        'yes' => 'sì',
        'no' => 'No',
        'caution' => 'Attenzione',
    ],
];
