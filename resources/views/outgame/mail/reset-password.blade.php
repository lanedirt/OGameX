<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ __('t_external.mail.reset_password.subject') }}</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body {
            margin: 0; padding: 0;
            background-color: #000000;
            background-image: url('{{ config('app.url') }}/img/outgame/1867da5b5f8769b547bb91d88bb4f8.jpg');
            background-repeat: no-repeat;
            background-position: center top;
            font-family: Helvetica, Arial, sans-serif;
        }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        a { color: #619fc8; text-decoration: none; font-weight: bold; }
        a:hover { color: #91b0c4; }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#000000;">

<!-- Outer wrapper -->
<table width="100%" border="0" cellpadding="0" cellspacing="0"
       style="background-color:#000000;background-image:url('{{ config('app.url') }}/img/outgame/1867da5b5f8769b547bb91d88bb4f8.jpg');background-repeat:no-repeat;background-position:center top;">
    <tr>
        <td align="center" style="padding:60px 16px 40px;">

            <!-- Card -->
            <table width="560" border="0" cellpadding="0" cellspacing="0"
                   style="max-width:560px;background-color:#22303f;border:2px solid #2d343c;border-radius:3px;">

                <!-- Header -->
                <tr>
                    <td align="center" style="padding:24px 32px 20px;border-bottom:1px dotted #000000;background-color:#1a2530;">
                        <img src="data:image/png;base64,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"
                             width="48" height="48" alt="OGameX"
                             style="display:block;margin:0 auto 10px;"/>
                        <p style="margin:0;font-size:22px;font-weight:bold;color:#619fc8;letter-spacing:3px;text-shadow:-1px -1px 0 #000;font-family:Helvetica,Arial,sans-serif;">
                            OGame<span style="color:#91b0c4;">X</span>
                        </p>
                        <p style="margin:4px 0 0;font-size:11px;color:#4579a4;letter-spacing:2px;text-transform:uppercase;font-family:Helvetica,Arial,sans-serif;">
                            Conquer the Universe
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:28px 32px 8px;">
                        <h2 style="margin:0 0 16px;font-size:15px;color:#619fc8;font-family:Helvetica,Arial,sans-serif;text-shadow:-1px -1px 0 #000;border-bottom:1px dotted #2e363e;padding-bottom:10px;">
                            {{ __('t_external.mail.reset_password.heading') }}
                        </h2>
                        <p style="margin:0 0 12px;font-size:12px;color:#848484;line-height:1.7;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.reset_password.greeting', ['username' => $username]) }}
                        </p>
                        <p style="margin:0 0 20px;font-size:12px;color:#848484;line-height:1.7;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.reset_password.body') }}
                        </p>
                    </td>
                </tr>

                <!-- CTA button -->
                <tr>
                    <td align="center" style="padding:8px 32px 24px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="background-color:#22303f;border:1px solid #3b4d5f;border-bottom:1px solid #23313f;border-radius:5px;">
                                    <a href="{{ $resetUrl }}"
                                       style="display:inline-block;padding:8px 28px 9px;font-size:13px;font-weight:bold;color:#ffffff;text-decoration:none;font-family:Helvetica,Arial,sans-serif;text-shadow:0 -1px 1px rgba(0,0,0,0.25);letter-spacing:1px;text-transform:uppercase;">
                                        {{ __('t_external.mail.reset_password.cta') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Notes -->
                <tr>
                    <td style="padding:0 32px 8px;">
                        <p style="margin:0 0 8px;font-size:11px;color:#4579a4;line-height:1.6;font-family:Helvetica,Arial,sans-serif;">
                            &#9203; {{ __('t_external.mail.reset_password.expiry') }}
                        </p>
                        <p style="margin:0 0 20px;font-size:11px;color:#4579a4;line-height:1.6;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.reset_password.no_action') }}
                        </p>
                    </td>
                </tr>

                <!-- Divider + URL fallback -->
                <tr>
                    <td style="padding:0 32px 28px;border-top:1px dotted #2e363e;">
                        <p style="margin:16px 0 6px;font-size:11px;color:#4579a4;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.reset_password.url_fallback') }}
                        </p>
                        <p style="margin:0;font-size:11px;word-break:break-all;font-family:Helvetica,Arial,sans-serif;">
                            <a href="{{ $resetUrl }}" style="color:#619fc8;font-weight:normal;">{{ $resetUrl }}</a>
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Card -->

            <!-- Footer -->
            <table width="560" border="0" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <tr>
                    <td align="center" style="padding:16px 0 0;">
                        <p style="margin:0;font-size:11px;color:#425463;font-family:Helvetica,Arial,sans-serif;">
                            &copy; OGameX &mdash; {{ __('t_external.footer.copyright') }}
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
