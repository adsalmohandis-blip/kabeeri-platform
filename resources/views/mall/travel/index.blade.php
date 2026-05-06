@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Travel and Tourism</span>
                <h1>Travel and Tourism</h1>
                <p>Travel listings with destination, availability, contact channels, moderation, and reporting.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search travel">
                    <button class="button primary" type="submit">Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Travel Index and Detail Upgrade</span>
                <h3>Destination trust</h3>
                <p>Travel offers need clear destination, contact policy, consent, and report path before inquiry.</p>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($listings as $listing)
                <article class="card">
                    <span class="chip">{{ $listing->listing_type }}</span>
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
        </div>

        {{ $listings->links() }}
    </section>
@endsection
