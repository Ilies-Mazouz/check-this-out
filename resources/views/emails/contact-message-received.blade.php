<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1a1a1a;">
    <h2>New contact message</h2>

    <p><strong>From:</strong> {{ $contactMessage->name }} ({{ $contactMessage->email }})</p>
    <p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>

    <p style="white-space: pre-line;">{{ $contactMessage->body }}</p>

    <p><a href="{{ route('admin.contact-messages.index') }}">View in admin panel</a></p>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
