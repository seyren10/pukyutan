@props(['url', 'text' => 'View in Puyo'])

{{--
    Renders as <x-mail-button :url="$url" text="Verify email address" /> from
    any mailable view. Table-wrapped and fully inline-styled (no reliance on
    the <style> block in layout.blade.php) since this is the one element
    that absolutely has to render right even in clients that strip <style>,
    like Outlook desktop.
--}}
<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 4px 0 8px;">
    <tr>
        <td style="border-radius:10px; background-color:#e38f00;">
            <a href="{{ $url }}" target="_blank"
                style="display:inline-block; padding:12px 28px; font-family:'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size:15px; font-weight:600; color:#25170d; text-decoration:none; border-radius:10px;">
                {{ $text }}
            </a>
        </td>
    </tr>
</table>