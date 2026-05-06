<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Travel and Tourism</h1>

        @forelse ($listings as $listing)
            <article>
                <h2>
                    <a href="{{ route('mall.travel.show', $listing) }}">{{ $listing->title }}</a>
                </h2>

                @if ($listing->destination)
                    <p>{{ $listing->destination }}</p>
                @endif

                @if ($listing->description)
                    <p>{{ $listing->description }}</p>
                @endif
            </article>
        @empty
            <p>No travel listings are published yet.</p>
        @endforelse

        {{ $listings->links() }}
    </main>
</body>
</html>
