@extends('emails.layout')

@section('title', "New request to join \"{$groupName}\"")
@section('preheader', "{$requesterName} wants to join \"{$groupName}\".")

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        New request to join &ldquo;{{ $groupName }}&rdquo;
    </h1>

    <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#362418;">
        <strong>{{ $requesterName }}</strong> has requested to join <strong>&ldquo;{{ $groupName }}&rdquo;</strong>.
        You can accept or reject this request at any time from the group's share requests.
    </p>

    <x-mail-button :url="$accessUrl" text="Review request" />
@endsection
