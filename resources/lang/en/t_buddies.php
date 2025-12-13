<?php

return [
    // Error messages
    'error' => [
        'cannot_send_to_self' => 'Cannot send buddy request to yourself.',
        'user_not_found' => 'User not found.',
        'cannot_send_to_admin' => 'Cannot send buddy requests to administrators.',
        'cannot_send_to_user' => 'Cannot send buddy request to this user.',
        'already_buddies' => 'You are already buddies with this user.',
        'request_exists' => 'A buddy request already exists between these users.',
        'request_not_found' => 'Buddy request not found.',
        'not_authorized_accept' => 'You are not authorized to accept this request.',
        'not_authorized_reject' => 'You are not authorized to reject this request.',
        'not_authorized_cancel' => 'You are not authorized to cancel this request.',
        'already_processed' => 'This request has already been processed.',
        'relationship_not_found' => 'Buddy relationship not found.',
        'cannot_ignore_self' => 'Cannot ignore yourself.',
        'already_ignored' => 'Player is already ignored.',
        'not_in_ignore_list' => 'Player is not in your ignored list.',
        'send_request_failed' => 'Failed to send buddy request.',
        'ignore_player_failed' => 'Failed to ignore player.',
        'delete_buddy_failed' => 'Failed to delete buddy',
        'search_too_short' => 'Too few characters! Please put in at least 2 characters.',
        'invalid_action' => 'Invalid action',
    ],

    // Success messages
    'success' => [
        'request_sent' => 'Buddy request sent successfully!',
        'request_cancelled' => 'Buddy request cancelled successfully.',
        'request_accepted' => 'Buddy request accepted!',
        'request_rejected' => 'Buddy request rejected',
        'request_accepted_symbol' => '✓ Buddy request accepted',
        'request_rejected_symbol' => '✗ Buddy request rejected',
        'buddy_deleted' => 'Buddy deleted successfully!',
        'player_ignored' => 'Player ignored successfully!',
        'player_unignored' => 'Player unignored successfully.',
    ],

    // UI labels and titles
    'ui' => [
        'page_title' => 'Buddies',
        'my_buddies' => 'My buddies',
        'ignored_players' => 'Ignored Players',
        'buddy_request' => 'buddy request',
        'buddy_request_title' => 'Buddy request',
        'buddy_request_to' => 'Buddy request to',
        'buddy_requests' => 'Buddy requests',
        'new_buddy_request' => 'New buddy request',
        'write_message' => 'Write message',
        'send_message' => 'Send message',
        'send' => 'send',
        'search_placeholder' => 'Search...',
        'no_buddies_found' => 'No buddies found',
        'no_buddy_requests' => 'You currently have no buddy requests.',
        'no_requests_sent' => 'You have not sent any buddy requests.',
        'no_ignored_players' => 'No ignored players',
        'requests_received' => 'requests received',
        'requests_sent' => 'requests sent',
        'new' => 'new',
        'new_label' => 'New',
        'from' => 'From:',
        'to' => 'To:',
        'online' => 'online',
        'received_request_from' => 'You have received a new buddy request from',
    ],

    // Actions
    'action' => [
        'accept_request' => 'Accept buddy request',
        'reject_request' => 'Reject buddy request',
        'withdraw_request' => 'Withdraw buddy request',
        'delete_buddy' => 'Delete buddy',
        'confirm_delete_buddy' => 'Do you really want to delete your buddy',
        'add_as_buddy' => 'Add as buddy',
        'ignore_player' => 'Are you sure you want to ignore',
        'remove_from_ignore' => 'Remove from ignore list',
        'report_message' => 'Report this message to a game operator?',
    ],

    // Table headers
    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'points' => 'Points',
        'rank' => 'Rank',
        'alliance' => 'Alliance',
        'coords' => 'Coords',
        'actions' => 'Actions',
    ],

    // Common
    'common' => [
        'yes' => 'yes',
        'no' => 'No',
        'caution' => 'Caution',
    ],
];
