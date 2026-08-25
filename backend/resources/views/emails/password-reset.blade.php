@extends('emails.layout')

@section('title', 'Reset your password')
@section('preheader', 'Reset your Puyo password. This link expires in 60 minutes.')

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        Reset your password
    </h1>

    <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#362418;">
        @if (!empty($name))
            Hi {{ $name }},
        @endif
        we received a request to reset the password for your Puyo account. Click the button below to choose a new one.
    </p>

    <x-mail-button :url="$url" text="Reset password" />

    <p style="margin:28px 0 0; font-size:13px; line-height:1.6; color:#6e6055;">
        This link expires in 60 minutes. If you didn't request a password reset, you can safely ignore this email —
        your password won't be changed.
    </p>

    <p style="margin:20px 0 0; padding-top:20px; border-top:1px solid #dbd3c6; font-size:12px; line-height:1.6; color:#6e6055; word-break:break-all;">
        Button not working? Paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#e38f00;">{{ $url }}</a>
    </p>
@endsection
