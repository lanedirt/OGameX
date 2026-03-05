<?php

return [
    // Foutmeldingen
    'error' => [
        'cannot_send_to_self' => 'Je kunt geen buddyverzoek naar jezelf sturen.',
        'user_not_found' => 'Gebruiker niet gevonden.',
        'cannot_send_to_admin' => 'Je kunt geen buddyverzoeken naar beheerders sturen.',
        'cannot_send_to_user' => 'Je kunt geen buddyverzoek naar deze gebruiker sturen.',
        'already_buddies' => 'Je bent al buddy met deze gebruiker.',
        'request_exists' => 'Er bestaat al een buddyverzoek tussen deze gebruikers.',
        'request_not_found' => 'Buddyverzoek niet gevonden.',
        'not_authorized_accept' => 'Je bent niet bevoegd om dit verzoek te accepteren.',
        'not_authorized_reject' => 'Je bent niet bevoegd om dit verzoek te weigeren.',
        'not_authorized_cancel' => 'Je bent niet bevoegd om dit verzoek te annuleren.',
        'already_processed' => 'Dit verzoek is al verwerkt.',
        'relationship_not_found' => 'Buddyrelatie niet gevonden.',
        'cannot_ignore_self' => 'Je kunt jezelf niet negeren.',
        'already_ignored' => 'Speler wordt al genegeerd.',
        'not_in_ignore_list' => 'Speler staat niet op je negeerlijst.',
        'send_request_failed' => 'Verzenden van buddyverzoek mislukt.',
        'ignore_player_failed' => 'Speler negeren mislukt.',
        'delete_buddy_failed' => 'Buddy verwijderen mislukt',
        'search_too_short' => 'Te weinig tekens! Voer minimaal 2 tekens in.',
        'invalid_action' => 'Ongeldige actie',
    ],

    // Succesmeldingen
    'success' => [
        'request_sent' => 'Buddyverzoek succesvol verzonden!',
        'request_cancelled' => 'Buddyverzoek succesvol geannuleerd.',
        'request_accepted' => 'Buddyverzoek geaccepteerd!',
        'request_rejected' => 'Buddyverzoek geweigerd',
        'request_accepted_symbol' => '✓ Buddyverzoek geaccepteerd',
        'request_rejected_symbol' => '✗ Buddyverzoek geweigerd',
        'buddy_deleted' => 'Buddy succesvol verwijderd!',
        'player_ignored' => 'Speler succesvol genegeerd!',
        'player_unignored' => 'Speler niet meer genegeerd.',
    ],

    // UI-labels en titels
    'ui' => [
        'page_title' => 'Buddy\'s',
        'my_buddies' => 'Mijn buddy\'s',
        'ignored_players' => 'Genegeerde spelers',
        'buddy_request' => 'buddyverzoek',
        'buddy_request_title' => 'Buddyverzoek',
        'buddy_request_to' => 'Buddyverzoek aan',
        'buddy_requests' => 'Buddyverzoeken',
        'new_buddy_request' => 'Nieuw buddyverzoek',
        'write_message' => 'Bericht schrijven',
        'send_message' => 'Bericht versturen',
        'send' => 'versturen',
        'search_placeholder' => 'Zoeken...',
        'no_buddies_found' => 'Geen buddy\'s gevonden',
        'no_buddy_requests' => 'Je hebt momenteel geen buddyverzoeken.',
        'no_requests_sent' => 'Je hebt geen buddyverzoeken verstuurd.',
        'no_ignored_players' => 'Geen genegeerde spelers',
        'requests_received' => 'verzoeken ontvangen',
        'requests_sent' => 'verzoeken verzonden',
        'new' => 'nieuw',
        'new_label' => 'Nieuw',
        'from' => 'Van:',
        'to' => 'Aan:',
        'online' => 'online',
        'status_on' => 'Aan',
        'status_off' => 'Uit',
        'received_request_from' => 'Je hebt een nieuw buddyverzoek ontvangen van',
        'buddy_request_to_player' => 'Buddyverzoek aan speler',
        'ignore_player_title' => 'Speler negeren',
    ],

    // Acties
    'action' => [
        'accept_request' => 'Buddyverzoek accepteren',
        'reject_request' => 'Buddyverzoek weigeren',
        'withdraw_request' => 'Buddyverzoek intrekken',
        'delete_buddy' => 'Buddy verwijderen',
        'confirm_delete_buddy' => 'Wil je je buddy echt verwijderen',
        'add_as_buddy' => 'Toevoegen als buddy',
        'ignore_player' => 'Weet je zeker dat je wilt negeren',
        'remove_from_ignore' => 'Verwijderen van negeerlijst',
        'report_message' => 'Dit bericht melden bij een speloperator?',
    ],

    // Tabelkoppen
    'table' => [
        'id' => 'ID',
        'name' => 'Naam',
        'points' => 'Punten',
        'rank' => 'Rang',
        'alliance' => 'Alliantie',
        'coords' => 'Coördinaten',
        'actions' => 'Acties',
    ],

    // Algemeen
    'common' => [
        'yes' => 'ja',
        'no' => 'Nee',
        'caution' => 'Let op',
    ],
];
