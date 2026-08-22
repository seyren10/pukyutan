@php
    $jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;

    $payload = json_encode([
        'source' => 'google-auth-popup',
        'status' => $status,
        'message' => $message,
    ], $jsonFlags);

    $targetOrigin = json_encode(config('app.frontend_url'), $jsonFlags);
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Signing in…</title>
</head>

<body>
    <p>You can close this window.</p>
    <script>
        (function () {
            var payload = {!! $payload !!};
            var targetOrigin = {!! $targetOrigin !!};

            if (window.opener) {
                window.opener.postMessage(payload, targetOrigin);
            }

            window.close();
        })();
    </script>
</body>

</html>