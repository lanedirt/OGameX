<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ __('t_external.mail.retrieve_email.subject') }}</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body {
            margin: 0; padding: 0;
            background-color: #ffffff;
            font-family: Helvetica, Arial, sans-serif;
        }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        a { color: #619fc8; text-decoration: none; font-weight: bold; }
        a:hover { color: #91b0c4; }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#ffffff;">

<table width="100%" border="0" cellpadding="0" cellspacing="0"
       style="background-color:#ffffff;">
    <tr>
        <td align="center" style="padding:60px 16px 40px;">

            <!-- Card -->
            <table width="560" border="0" cellpadding="0" cellspacing="0"
                   style="max-width:560px;background-color:#22303f;border:2px solid #2d343c;border-radius:3px;">

                <!-- Header -->
                <tr>
                    <td align="center" style="padding:24px 32px 20px;border-bottom:1px dotted #000000;background-color:#1a2530;">
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
                            {{ __('t_external.mail.retrieve_email.heading') }}
                        </h2>
                        <p style="margin:0 0 12px;font-size:12px;color:#848484;line-height:1.7;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.retrieve_email.greeting', ['username' => $username]) }}
                        </p>
                        <p style="margin:0 0 12px;font-size:12px;color:#848484;line-height:1.7;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.retrieve_email.body') }}
                        </p>
                    </td>
                </tr>

                <!-- Email hint box -->
                <tr>
                    <td style="padding:0 32px 24px;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center"
                                    style="background-color:#1e2b39;border-top:1px solid #2e363e;border-left:1px solid #1d2228;border-right:1px solid #1d2228;border-bottom:1px solid #000;padding:14px;font-size:18px;font-weight:bold;color:#619fc8;letter-spacing:3px;font-family:Helvetica,Arial,sans-serif;">
                                    {{ $maskedEmail }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- CTA button -->
                <tr>
                    <td align="center" style="padding:0 32px 24px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="background-color:#22303f;border:1px solid #3b4d5f;border-bottom:1px solid #23313f;border-radius:5px;">
                                    <a href="{{ $loginUrl }}"
                                       style="display:inline-block;padding:8px 28px 9px;font-size:13px;font-weight:bold;color:#ffffff;text-decoration:none;font-family:Helvetica,Arial,sans-serif;text-shadow:0 -1px 1px rgba(0,0,0,0.25);letter-spacing:1px;text-transform:uppercase;">
                                        {{ __('t_external.mail.retrieve_email.cta') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer note -->
                <tr>
                    <td style="padding:0 32px 24px;border-top:1px dotted #2e363e;">
                        <p style="margin:16px 0 0;font-size:11px;color:#4579a4;line-height:1.6;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.mail.retrieve_email.no_action') }}
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Card -->

            <!-- Footer -->
            <table width="560" border="0" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <tr>
                    <td align="center" style="padding:16px 0 0;">
                        <p style="margin:0;font-size:11px;color:#000000;font-family:Helvetica,Arial,sans-serif;">
                            {{ __('t_external.footer.copyright') }}
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
