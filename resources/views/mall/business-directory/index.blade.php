<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Business Directory</h1>

        @forelse ($businesses as $business)
            <article>
                <h2>
                    <a href="{{ route('mall.businesses.show', $business) }}">{{ $business->display_name }}</a>
                </h2>

                @if ($business->description)
                    <p>{{ $business->description }}</p>
                @endif
            </article>
        @empty
            <p>No businesses are published yet.</p>
        @endforelse

        {{ $businesses->links() }}
    </main>
</body>
</html>
