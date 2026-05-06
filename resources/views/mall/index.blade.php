<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        @include('mall.partials.navigation')

        <h1>KABEERI Mall</h1>

        <section aria-label="Mall sections">
            @foreach ($sections as $section)
                <article>
                    <h2>
                        <a href="{{ $section['route'] }}">{{ $section['label'] }}</a>
                    </h2>
                    <p>{{ $section['count'] }} published</p>
                </article>
            @endforeach
        </section>
    </main>
</body>
</html>
