<?php

return [
    // Error messages
    'error' => [
        'cannot_send_to_self' => 'Du kannst dir selbst keine Buddyanfrage senden.',
        'user_not_found' => 'Spieler nicht gefunden.',
        'cannot_send_to_admin' => 'Du kannst Administratoren keine Buddyanfragen senden.',
        'cannot_send_to_user' => 'Du kannst diesem Spieler keine Buddyanfrage senden.',
        'already_buddies' => 'Du bist bereits mit diesem Spieler befreundet.',
        'request_exists' => 'Zwischen diesen Spielern existiert bereits eine Buddyanfrage.',
        'request_not_found' => 'Buddyanfrage nicht gefunden.',
        'not_authorized_accept' => 'Du bist nicht berechtigt, diese Anfrage anzunehmen.',
        'not_authorized_reject' => 'Du bist nicht berechtigt, diese Anfrage abzulehnen.',
        'not_authorized_cancel' => 'Du bist nicht berechtigt, diese Anfrage abzubrechen.',
        'already_processed' => 'Diese Anfrage wurde bereits bearbeitet.',
        'relationship_not_found' => 'Buddyverbindung nicht gefunden.',
        'cannot_ignore_self' => 'Du kannst dich nicht selbst ignorieren.',
        'already_ignored' => 'Spieler wird bereits ignoriert.',
        'not_in_ignore_list' => 'Spieler befindet sich nicht auf deiner Ignorierliste.',
        'send_request_failed' => 'Buddyanfrage konnte nicht gesendet werden.',
        'ignore_player_failed' => 'Spieler konnte nicht ignoriert werden.',
        'delete_buddy_failed' => 'Buddy konnte nicht gelöscht werden',
        'search_too_short' => 'Zu wenige Zeichen! Bitte gib mindestens 2 Zeichen ein.',
        'invalid_action' => 'Ungültige Aktion',
    ],

    // Success messages
    'success' => [
        'request_sent' => 'Buddyanfrage erfolgreich gesendet!',
        'request_cancelled' => 'Buddyanfrage erfolgreich abgebrochen.',
        'request_accepted' => 'Buddyanfrage angenommen!',
        'request_rejected' => 'Buddyanfrage abgelehnt',
        'request_accepted_symbol' => '✓ Buddyanfrage angenommen',
        'request_rejected_symbol' => '✗ Buddyanfrage abgelehnt',
        'buddy_deleted' => 'Buddy erfolgreich gelöscht!',
        'player_ignored' => 'Spieler erfolgreich ignoriert!',
        'player_unignored' => 'Spieler nicht mehr ignoriert.',
    ],

    // UI labels and titles
    'ui' => [
        'page_title' => 'Buddies',
        'my_buddies' => 'Meine Buddies',
        'ignored_players' => 'Ignorierte Spieler',
        'buddy_request' => 'Buddyanfrage',
        'buddy_request_title' => 'Buddyanfrage',
        'buddy_request_to' => 'Buddyanfrage an',
        'buddy_requests' => 'Buddyanfragen',
        'new_buddy_request' => 'Neue Buddyanfrage',
        'write_message' => 'Nachricht schreiben',
        'send_message' => 'Nachricht senden',
        'send' => 'senden',
        'search_placeholder' => 'Suchen...',
        'no_buddies_found' => 'Keine Buddies gefunden',
        'no_buddy_requests' => 'Du hast derzeit keine Buddyanfragen.',
        'no_requests_sent' => 'Du hast keine Buddyanfragen gesendet.',
        'no_ignored_players' => 'Keine ignorierten Spieler',
        'requests_received' => 'Anfragen erhalten',
        'requests_sent' => 'Anfragen gesendet',
        'new' => 'neu',
        'new_label' => 'Neu',
        'from' => 'Von:',
        'to' => 'An:',
        'online' => 'online',
        'status_on' => 'An',
        'status_off' => 'Aus',
        'received_request_from' => 'Du hast eine neue Buddyanfrage erhalten von',
        'buddy_request_to_player' => 'Buddyanfrage an Spieler',
        'ignore_player_title' => 'Spieler ignorieren',
    ],

    // Actions
    'action' => [
        'accept_request' => 'Buddyanfrage annehmen',
        'reject_request' => 'Buddyanfrage ablehnen',
        'withdraw_request' => 'Buddyanfrage zurückziehen',
        'delete_buddy' => 'Buddy löschen',
        'confirm_delete_buddy' => 'Möchtest du deinen Buddy wirklich löschen',
        'add_as_buddy' => 'Als Buddy hinzufügen',
        'ignore_player' => 'Bist du sicher, dass du ignorieren möchtest',
        'remove_from_ignore' => 'Von der Ignorierliste entfernen',
        'report_message' => 'Diese Nachricht an einen Spieloperator melden?',
    ],

    // Table headers
    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'points' => 'Punkte',
        'rank' => 'Rang',
        'alliance' => 'Allianz',
        'coords' => 'Koordinaten',
        'actions' => 'Aktionen',
    ],

    // Common
    'common' => [
        'yes' => 'ja',
        'no' => 'Nein',
        'caution' => 'Achtung',
    ],
];
