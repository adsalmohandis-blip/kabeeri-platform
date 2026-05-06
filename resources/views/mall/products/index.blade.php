@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Products</span>
                <h1>Products</h1>
                <p>Product mirrors published into Mall with price clarity, consent, moderation, and report flow.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search products">
                    <button class="button primary" type="submit">Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Product Index and Detail Upgrade</span>
                <h3>Public product trust</h3>
                <p>Prices, SKU attributes, images, consent, and reporting should be visible before lead or purchase intent.</p>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($products as $product)
                <article class="card">
                    <span class="chip">Product</span>
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
        </div>

        {{ $products->links() }}
    </section>
@endsection
