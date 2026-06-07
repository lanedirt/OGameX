<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outgame / Landing page - English
    |--------------------------------------------------------------------------
    */

    // Browser outdated warning
    'browser_warning' => [
        'title'  => 'Your browser is not up to date.',
        'desc1'  => 'Your Internet Explorer version does not correspond to the existing standards and is not supported by this website anymore.',
        'desc2'  => 'To use this website please update your web browser to a current version or use another web browser. If you are already using the latest version, please reload the page to display it properly.',
        'desc3'  => "Here's a list of the most popular browsers. Click on one of the symbols to get to the download page:",
    ],

    // Login form (header)
    'login' => [
        'page_title'        => 'OGame - Conquer the universe',
        'btn'               => 'Login',
        'email_label'       => 'Email address:',
        'password_label'    => 'Password:',
        'universe_label'    => 'Universe:',
        'universe_option_1' => '1. Universe',
        'submit'            => 'Log in',
        'forgot_password'   => 'Forgot your password?',
        'forgot_email'      => 'Forgot your email address?',
        'terms_accept_html' => 'With the login I accept the <a class="" href="#" target="_blank" title="T&amp;Cs">T&amp;Cs</a>',
    ],

    // Registration form (sidebar)
    'register' => [
        'play_free'    => 'PLAY FOR FREE!',
        'email_label'  => 'Email address:',
        'password_label' => 'Password:',
        'universe_label' => 'Universe:',
        'distinctions' => 'Distinctions',
        'terms_html'   => 'Our <a class="" target="_blank" href="#" title="T&amp;Cs"> T&amp;Cs </a> and <a class="" target="_blank" href="#" title="Privacy Policy"> Privacy Policy </a> apply in the game',
        'submit'       => 'Register',
    ],

    // Top navigation tabs
    'nav' => [
        'home'  => 'Home',
        'about' => 'About OGame',
        'media' => 'Media',
        'wiki'  => 'Wiki',
    ],

    // Home tab content
    'home' => [
        'title'            => 'OGame - Conquer the universe',
        'description_html' => '<em>OGame</em> is a strategy game set in space, with thousands of players from across the world competing at the same time. You only need a regular web browser to play.',
        'board_btn'        => 'Board',
        'trailer_title'    => 'Trailer',
    ],

    // Footer
    'footer' => [
        'legal'          => 'Legal',
        'privacy_policy' => 'Privacy Policy',
        'terms'          => 'T&Cs',
        'contact'        => 'Contact',
        'rules'          => 'Rules',
        'copyright'      => '© OGameX. All rights reserved.',
    ],

    // Inline JS strings
    'js' => [
        'login'            => 'Login',
        'close'            => 'Close',
        'age_check_failed' => 'We are sorry, but you are not eligible to register. Please see our T&C for more information.',
    ],

    // jQuery ValidationEngine strings
    'validation' => [
        'required'                  => 'This field is required',
        'make_decision'             => 'Make a decision',
        'accept_terms'              => 'You must accept the T&Cs.',
        'length'                    => 'Between 3 and 20 characters allowed.',
        'pw_length'                 => 'Between 4 and 20 characters allowed.',
        'email'                     => 'You need to enter a valid email address!',
        'invalid_chars'             => 'Contains invalid characters.',
        'no_begin_end_underscore'   => 'Your name may not start or end with an underscore.',
        'no_begin_end_whitespace'   => 'Your name may not start or end with a space.',
        'max_three_underscores'     => 'Your name may not contain more than 3 underscores in total.',
        'max_three_whitespaces'     => 'Your name may not include more than 3 spaces in total.',
        'no_consecutive_underscores' => 'You may not use two or more underscores one after the other.',
        'no_consecutive_whitespaces' => 'You may not use two or more spaces one after the other.',
        'username_available'        => 'This username is available.',
        'username_loading'          => 'Please wait, loading...',
        'username_taken'            => 'This username is not available anymore.',
        'only_letters'              => 'Use characters only.',
    ],

    // Forgot password page
    'forgot_password' => [
        'title'          => 'Forgot your password?',
        'description'    => 'Enter your email address below and we will send you a link to reset your password.',
        'email_label'    => 'Email address:',
        'submit'         => 'Send reset link',
        'back_to_login'  => '← Back to login',
    ],

    // Reset password page
    'reset_password' => [
        'title'          => 'Reset your password',
        'email_label'    => 'Email address:',
        'password_label' => 'New password:',
        'confirm_label'  => 'Confirm new password:',
        'submit'         => 'Reset password',
    ],

    // Forgot email page
    'forgot_email' => [
        'title'          => 'Forgot your email address?',
        'description'    => 'Enter your commander name and we will send a hint to the registered email address.',
        'username_label' => 'Commander name:',
        'submit'         => 'Send hint',
        'back_to_login'  => '← Back to login',
        'sent'           => 'If a matching account was found, a hint has been sent to the registered email address.',
    ],

    // Outgoing email templates
    'mail' => [
        'reset_password' => [
            'subject'     => 'Reset your OGameX password',
            'heading'     => 'Password Reset',
            'greeting'    => 'Hello :username,',
            'body'        => 'We received a request to reset the password for your account. Click the button below to choose a new password.',
            'cta'         => 'Reset Password',
            'expiry'      => 'This link will expire in 60 minutes.',
            'no_action'   => 'If you did not request a password reset, no further action is required.',
            'url_fallback' => 'If you have trouble clicking the button, copy and paste the URL below into your browser:',
        ],
        'retrieve_email' => [
            'subject'   => 'Your OGameX email address',
            'heading'   => 'Email Address Hint',
            'greeting'  => 'Hello :username,',
            'body'      => 'You requested a hint for the email address associated with your account:',
            'cta'       => 'Go to Login',
            'no_action' => 'If you did not make this request, you can safely ignore this email.',
        ],
    ],

    // Universe selection characteristics tooltip texts
    'universe_characteristics' => [
        'fleet_speed'     => 'Fleet Speed: the higher the value, the less time you have left to react to an attack.',
        'economy_speed'   => 'Economy Speed: the higher the value, the faster constructions and research will be completed and resources gathered.',
        'debris_ships'    => 'Some of the ships destroyed in battle will enter the debris field.',
        'debris_defence'  => 'Some of the defensive structures destroyed in battle will enter the debris field.',
        'dark_matter_gift' => 'You will receive Dark Matter as a reward for confirming your email address.',
        'aks_on'          => 'Alliance battle system activated',
        'planet_fields'   => 'The maximum amount of building slots has been increased.',
        'wreckfield'      => 'Space Dock activated: some destroyed ships can be restored using the Space Dock.',
        'universe_big'    => 'Amount of Galaxies in the Universe',
    ],
];
