@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Services</span>
                <h1>Services</h1>
                <p>Service listings help visitors request help while understanding category, availability, consent, and moderation.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search services">
                    <button class="button primary" type="submit"><x-kabeeri-icon name="search" />Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Service Index and Detail Upgrade</span>
                <h3>Lead-ready services</h3>
                <p>Services should explain category, price/rate where available, availability, and safe inquiry route.</p>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($services as $service)
                <article class="card">
                    <span class="chip">{{ $service->service_category ?: 'Service' }}</span>
                    <h2>
                        <a href="{{ route('mall.services.show', $service) }}">{{ $service->service_name }}</a>
                    </h2>

                    @if ($service->service_category)
                        <p>{{ $service->service_category }}</p>
                    @endif

                    @if ($service->description)
                        <p>{{ $service->description }}</p>
                    @endif
                </article>
            @empty
                <p>No services are published yet.</p>
            @endforelse
        </div>

        {{ $services->links() }}
    </section>
@endsection
