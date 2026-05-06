@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.travel.index') }}">Travel and Tourism</a>
                <h1>{{ $listing->title }}</h1>

                @if ($listing->destination)
                    <p>{{ $listing->destination }}</p>
                @endif

                @if ($listing->price_from)
                    <p>{{ $listing->currency }} {{ $listing->price_from }}</p>
                @endif

                @if ($listing->description)
                    <p>{{ $listing->description }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Travel trust</span>
                <h3>Inquiry safety</h3>
                <p>Travel listings should expose contact channels only when intended and keep report paths clear.</p>
            </aside>
        </div>
    </section>
@endsection
