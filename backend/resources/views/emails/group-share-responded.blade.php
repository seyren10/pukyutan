@extends('emails.layout')

@section('title', $accepted ? "You've been added to \"{$groupName}\"" : "Your request to join \"{$groupName}\" was declined")
@section('preheader', $accepted ? "You now have access to \"{$groupName}\"." : "Your request to join \"{$groupName}\" was declined.")

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        @if ($accepted)
            You're in!
        @else
            Request declined
        @endif
    </h1>

    <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#362418;">
        @if ($accepted)
            Your request to join <strong>&ldquo;{{ $groupName }}&rdquo;</strong> has been accepted. You can now view
            the group.
        @else
            Your request to join <strong>&ldquo;{{ $groupName }}&rdquo;</strong> was declined by the group owner.
        @endif
    </p>

    @if ($accepted)
        <x-mail-button :url="$groupUrl" text="View group" />
    @endif
@endsection
