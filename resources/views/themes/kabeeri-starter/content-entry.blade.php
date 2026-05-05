<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $contentEntry->title }} | {{ $site->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 24px;">
<main style="max-width: 820px; margin: 0 auto;">
    <p style="margin: 0 0 12px; color: #555;">{{ $site->name }}</p>
    <h1 style="margin: 0 0 12px;">{{ $contentEntry->title }}</h1>
    @if($contentEntry->excerpt)
        <p style="margin: 0 0 20px; color: #333;">{{ $contentEntry->excerpt }}</p>
    @endif
    <article>
        {!! nl2br(e($contentEntry->body ?? '')) !!}
    </article>
</main>
</body>
</html>
