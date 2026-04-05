<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('t_external.mail.reset_password.subject') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0b0b1a; color: #c8c8c8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1a2e; border: 1px solid #333; padding: 30px; }
        h1 { color: #f0a000; font-size: 22px; margin-top: 0; }
        p { line-height: 1.6; }
        .btn { display: inline-block; margin: 20px 0; padding: 12px 24px; background: #f0a000; color: #000; text-decoration: none; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
        .url { word-break: break-all; color: #f0a000; }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ __('t_external.mail.reset_password.heading') }}</h1>

    <p>{{ __('t_external.mail.reset_password.greeting', ['username' => $username]) }}</p>
    <p>{{ __('t_external.mail.reset_password.body') }}</p>

    <a href="{{ $resetUrl }}" class="btn">{{ __('t_external.mail.reset_password.cta') }}</a>

    <p>{{ __('t_external.mail.reset_password.expiry') }}</p>
    <p>{{ __('t_external.mail.reset_password.no_action') }}</p>

    <p>{{ __('t_external.mail.reset_password.url_fallback') }}<br>
        <span class="url">{{ $resetUrl }}</span>
    </p>

    <div class="footer">
        <p>{{ config('app.name') }}</p>
    </div>
</div>
</body>
</html>
