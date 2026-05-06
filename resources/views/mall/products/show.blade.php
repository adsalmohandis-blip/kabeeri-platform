<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.products.index') }}">Products</a>

        <h1>{{ $product->product_name }}</h1>

        @if ($product->price)
            <p>{{ $product->currency }} {{ $product->price }}</p>
        @endif

        @if ($product->description)
            <p>{{ $product->description }}</p>
        @endif

        @if ($product->attributes)
            <dl>
                @foreach ($product->attributes as $label => $value)
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
