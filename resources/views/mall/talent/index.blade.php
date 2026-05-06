<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Talent</h1>

        @forelse ($talent as $profile)
            <article>
                <h2>
                    <a href="{{ route('mall.talent.show', $profile) }}">{{ $profile->display_name }}</a>
                </h2>

                @if ($profile->headline)
                    <p>{{ $profile->headline }}</p>
                @endif
            </article>
        @empty
            <p>No talent profiles are published yet.</p>
        @endforelse

        {{ $talent->links() }}
    </main>
</body>
</html>
