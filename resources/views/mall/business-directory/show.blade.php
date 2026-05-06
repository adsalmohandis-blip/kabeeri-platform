@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.businesses.index') }}">Business Directory</a>
                <h1>{{ $business->display_name }}</h1>

                @if ($business->description)
                    <p>{{ $business->description }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Mall trust</span>
                <h3>Claim, report, and verify</h3>
                <p>This public profile can connect to Rabet/company verification, moderation cases, reviews, and listing claim requests.</p>
                <div class="actions" style="margin-top: 14px;">
                    <a class="button" href="{{ route('mall.claim-report') }}">Claim / Report</a>
                    <a class="button" href="{{ route('mall.trust') }}">Trust Center</a>
                </div>
            </aside>
        </div>
    </section>

    @if ($business->public_contacts)
        <section class="section">
            <div class="section-title">
                <div>
                    <span class="chip">Public contacts</span>
                    <h2>Contact channels</h2>
                </div>
                <p>Only explicitly public contact fields should appear on Mall pages.</p>
            </div>

            <div class="grid-3">
                @foreach ($business->public_contacts as $label => $value)
                    @if ($value)
                        <article class="card">
                            <dt>{{ str($label)->replace('_', ' ')->title() }}</dt>
                            <dd>{{ $value }}</dd>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
@endsection
