@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.products.index') }}">Products</a>
                <h1>{{ $product->product_name }}</h1>

                @if ($product->price)
                    <p>{{ $product->currency }} {{ $product->price }}</p>
                @endif

                @if ($product->description)
                    <p>{{ $product->description }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Product trust</span>
                <h3>Consent mirror</h3>
                <p>Product detail pages should keep consent, moderation, reporting, and public attributes visible.</p>
            </aside>
        </div>
    </section>

    @if ($product->attributes)
        <section class="section">
            <div class="section-title">
                <div>
                    <span class="chip">Attributes</span>
                    <h2>Product details</h2>
                </div>
                <p>Public attributes help visitors understand the product without exposing internal admin data.</p>
            </div>

            <div class="grid-3">
                @foreach ($product->attributes as $label => $value)
                    @if ($value)
                        <article class="card">
                            <dt>{{ str($label)->replace('_', ' ')->title() }}</dt>
                            <dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
@endsection
