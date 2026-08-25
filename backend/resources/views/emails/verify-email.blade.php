@extends('emails.layout')

@section('title', 'Verify your email address')
@section('preheader', 'Confirm your email to start creating and managing groups on Puyo.')

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        Verify your email address
    </h1>

    <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#362418;">
        @if (!empty($name))
            Hi {{ $name }},
        @endif
        thanks for signing up for Puyo. Click the button below to confirm this is your email address —
        you'll need to do this before you can create or manage a paluwagan group.
    </p>

    <x-mail-button :url="$url" text="Verify email address" />

    <p style="margin:28px 0 0; font-size:13px; line-height:1.6; color:#6e6055;">
        This link expires in 60 minutes. If you didn't create a Puyo account, you can safely ignore this email.
    </p>

    <p style="margin:20px 0 0; padding-top:20px; border-top:1px solid #dbd3c6; font-size:12px; line-height:1.6; color:#6e6055; word-break:break-all;">
        Button not working? Paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#e38f00;">{{ $url }}</a>
    </p>
@endsection