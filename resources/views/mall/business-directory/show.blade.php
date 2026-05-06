<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.businesses.index') }}">Business Directory</a>

        <h1>{{ $business->display_name }}</h1>

        @if ($business->description)
            <p>{{ $business->description }}</p>
        @endif

        @if ($business->public_contacts)
            <dl>
                @foreach ($business->public_contacts as $label => $value)
                    @if ($value)
                        <dt>{{ str($label)->replace('_', ' ')->title() }}</dt>
                        <dd>{{ $value }}</dd>
                    @endif
                @endforeach
            </dl>
        @endif
    </main>
</body>
</html>
