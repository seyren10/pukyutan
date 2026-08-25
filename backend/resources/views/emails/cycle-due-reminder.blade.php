@extends('emails.layout')

@section('title', "Upcoming contribution due — {$groupName}")
@section('preheader', "Your contribution for \"{$groupName}\" is due on {$dueDate}.")

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        Upcoming contribution due
    </h1>

    <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#362418;">
        Your contribution of <strong>₱{{ $contributionAmount }}</strong> for
        <strong>&ldquo;{{ $groupName }}&rdquo;</strong> is due on <strong>{{ $dueDate }}</strong>.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="margin: 0 0 24px; border:1px solid #dbd3c6; border-radius:12px; background-color:#ede7dd;">
        <tr>
            <td style="padding: 16px 20px; font-size:13px; line-height:1.6; color:#6e6055;">
                Cycle #{{ $cycleNumber }}
            </td>
        </tr>
    </table>

    <x-mail-button :url="$groupUrl" text="View group" />

    <p style="margin:28px 0 0; font-size:13px; line-height:1.6; color:#6e6055;">
        Already paid? You can disregard this reminder — the group owner marks contributions as received manually.
    </p>
@endsection
