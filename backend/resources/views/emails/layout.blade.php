<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', config('app.name', 'Puyo'))</title>

    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        table { border-collapse: collapse; }
        .email-h1, .email-body, .email-muted { font-family: Georgia, 'Times New Roman', serif !important; }
    </style>
    <![endif]-->

    <style>
        /*
         * Reused tokens (kept in sync with resources/css/app's --primary /
         * --foreground / --border etc.) — email clients can't read CSS
         * custom properties or oklch(), so these are the same palette
         * pre-converted to hex. If the app theme changes, update both.
         */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #fbf4e6;
        }

        a {
            color: #e38f00;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                width: 100% !important;
            }

            .email-content,
            .email-header,
            .email-footer-inner {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .email-h1 {
                font-size: 21px !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background-color:#fbf4e6;">

    {{-- Preheader: the snippet clients show next to the subject line. Hidden
    in the body, and padded with zero-width joiners so trailing template
    boilerplate doesn't leak into the preview. --}}
    <div
        style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#fbf4e6;">
        @yield('preheader', '')
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fbf4e6;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" class="email-wrapper"
                    style="width:600px; max-width:600px;">

                    {{-- Brand --}}
                    <tr>
                        <td class="email-header" style="padding: 0 8px 24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="34" height="34" align="center" valign="middle">
                                        <img src="{{ asset('puyo.png') }}" style="width:34px; height:34px;" />
                                    </td>
                                    <td width="10">&nbsp;</td>
                                    <td valign="middle">
                                        <span
                                            style="font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:19px; color:#362418;">Puyo</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color:#fffbf3; border:1px solid #dbd3c6; border-radius:16px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="email-content"
                                        style="padding: 40px 40px 32px; font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color:#362418;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="email-footer-inner" align="center" style="padding: 28px 8px 0;">
                            <p
                                style="margin:0; font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size:12px; line-height:18px; color:#6e6055; text-align:center;">
                                @yield('footer', '© ' . date('Y') . ' Puyo. Built for paluwagan groups everywhere.')
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>