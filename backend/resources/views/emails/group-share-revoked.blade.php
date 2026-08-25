@extends('emails.layout')

@section('title', "Your access to \"{$groupName}\" was removed")
@section('preheader', "Your access to \"{$groupName}\" was removed.")

@section('content')
    <h1 class="email-h1"
        style="margin:0 0 16px; font-family: Fraunces, Georgia, 'Times New Roman', serif; font-weight:600; font-size:24px; line-height:1.3; color:#362418;">
        Access removed
    </h1>

    <p style="margin:0; font-size:15px; line-height:1.6; color:#362418;">
        The owner of <strong>&ldquo;{{ $groupName }}&rdquo;</strong> has removed your access to this group. If you
        think this was a mistake, reach out to the group owner directly.
    </p>
@endsection
