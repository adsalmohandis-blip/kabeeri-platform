<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.services.index') }}">Services</a>

        <h1>{{ $service->service_name }}</h1>

        @if ($service->service_category)
            <p>{{ $service->service_category }}</p>
        @endif

        @if ($service->hourly_rate)
            <p>{{ $service->currency }} {{ $service->hourly_rate }}</p>
        @endif

        @if ($service->description)
            <p>{{ $service->description }}</p>
        @endif

        @if ($service->availability_info)
            <dl>
                @foreach ($service->availability_info as $label => $value)
                    @if ($value)
                        <dt>{{ str($label)->replace('_', ' ')->title() }}</dt>
                        <dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                    @endif
                @endforeach
            </dl>
        @endif
    </main>
</body>
</html>
