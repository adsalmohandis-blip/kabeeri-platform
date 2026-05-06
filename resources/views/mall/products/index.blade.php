<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Products</h1>

        @forelse ($products as $product)
            <article>
                <h2>
                    <a href="{{ route('mall.products.show', $product) }}">{{ $product->product_name }}</a>
                </h2>

                @if ($product->price)
                    <p>{{ $product->currency }} {{ $product->price }}</p>
                @endif

                @if ($product->description)
                    <p>{{ $product->description }}</p>
                @endif
            </article>
        @empty
            <p>No products are published yet.</p>
        @endforelse

        {{ $products->links() }}
    </main>
</body>
</html>
