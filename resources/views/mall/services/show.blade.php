@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.services.index') }}">Services</a>
                <h1>{{ $service->service_name }}</h1>

                @if ($service->service_category)
                    <p>{{ $service->service_category }}</p>
                @endif

                @if ($service->hourly_rate)
                    <p>{{ $service->currency }} {{ $service->hourly_rate }}</p>
                @endif

                @if ($service->description)
                    <p>{{ $service->description }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Service trust</span>
                <h3>Request safely</h3>
                <p>Service detail pages should show availability, expected handoff, and claim/report routes before conversion.</p>
            </aside>
        </div>
    </section>

    @if ($service->availability_info)
        <section class="section">
            <div class="section-title">
                <div>
                    <span class="chip">Availability</span>
                    <h2>Service availability</h2>
                </div>
                <p>Availability is public only when the owner chooses to publish it.</p>
            </div>

            <div class="grid-3">
                @foreach ($service->availability_info as $label => $value)
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
