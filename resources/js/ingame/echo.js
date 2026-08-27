/**
 * Laravel Echo initialization for real-time chat via Laravel Reverb.
 *
 * This file sets up the WebSocket connection using Laravel Echo with the
 * Reverb (Pusher-compatible) broadcaster. It must be loaded before chat.js.
 *
 * The variables reverbAppKey, reverbHost, reverbPort, and reverbScheme
 * are expected to be set in the main Blade layout before this script loads.
 */
(function () {
    // Only initialize if Reverb config variables are available
    if (typeof reverbAppKey === 'undefined' || !reverbAppKey) {
        return;
    }

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbAppKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
})();
