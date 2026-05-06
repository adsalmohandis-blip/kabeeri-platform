@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.talent.index') }}">Talent</a>
                <h1>{{ $talent->display_name }}</h1>

                @if ($talent->headline)
                    <p>{{ $talent->headline }}</p>
                @endif

                @if ($talent->bio)
                    <p>{{ $talent->bio }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Professional trust</span>
                <h3>Skills and availability</h3>
                @if ($talent->skills)
                    <p>{{ implode(', ', $talent->skills) }}</p>
                @endif

                @if ($talent->location_label)
                    <p>{{ $talent->location_label }}</p>
                @endif
            </aside>
        </div>
    </section>
@endsection
