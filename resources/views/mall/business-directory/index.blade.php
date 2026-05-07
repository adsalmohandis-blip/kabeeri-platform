@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Business Directory</span>
                <h1>Business Directory</h1>
                <p>Published business profiles with consent, public contacts, verification context, and claim/report paths.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search businesses">
                    <button class="button primary" type="submit"><x-kabeeri-icon name="search" />Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Business Directory Index and Detail Upgrade</span>
                <h3>Trust signals</h3>
                <p>BusinessProfile, Rabet identity, public contacts, publication consent, moderation, and reporting all belong to this journey.</p>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($businesses as $business)
                <article class="card">
                    <span class="chip">Published Business</span>
                    <h2>
                        <a href="{{ route('mall.businesses.show', $business) }}">{{ $business->display_name }}</a>
                    </h2>

                    @if ($business->description)
                        <p>{{ $business->description }}</p>
                    @endif

                    <p>Published with consent · Claim/report available</p>
                </article>
            @empty
                <p>No businesses are published yet.</p>
            @endforelse
        </div>

        {{ $businesses->links() }}
    </section>
@endsection
