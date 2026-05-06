<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Services</h1>

        @forelse ($services as $service)
            <article>
                <h2>
                    <a href="{{ route('mall.services.show', $service) }}">{{ $service->service_name }}</a>
                </h2>

                @if ($service->service_category)
                    <p>{{ $service->service_category }}</p>
                @endif

                @if ($service->description)
                    <p>{{ $service->description }}</p>
                @endif
            </article>
        @empty
            <p>No services are published yet.</p>
        @endforelse

        {{ $services->links() }}
    </main>
</body>
</html>
