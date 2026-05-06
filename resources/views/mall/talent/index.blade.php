@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Talent</span>
                <h1>Talent</h1>
                <p>Public professional profiles with skills, availability, location, Academy badges, and publication consent.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search talent">
                    <button class="button primary" type="submit">Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Public Talent Console and Professional Profile Path</span>
                <h3>Professional profile path</h3>
                <p>Talent listings should connect consent, evidence, skills, badges, moderation, and report flow.</p>
                <div class="actions" style="margin-top: 14px;">
                    <a class="button" href="{{ route('network.talent-path') }}">Talent Path</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($talent as $profile)
                <article class="card">
                    <span class="chip">Professional Profile</span>
                    <h2>
                        <a href="{{ route('mall.talent.show', $profile) }}">{{ $profile->display_name }}</a>
                    </h2>

                    @if ($profile->headline)
                        <p>{{ $profile->headline }}</p>
                    @endif
                </article>
            @empty
                <p>No talent profiles are published yet.</p>
            @endforelse
        </div>

        {{ $talent->links() }}
    </section>
@endsection
