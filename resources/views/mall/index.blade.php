@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">{{ __('kabeeri.brand.name') }} Mall Home</span>
                <h1>{{ __('kabeeri.brand.name') }} Mall</h1>
                <p>Public discovery for trusted businesses, products, services, courses, talent, and travel. Mall is public discovery; Marketplace is internal extensions.</p>
                <form class="search" action="{{ route('mall.search') }}" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="ابحث عن شركة، منتج، خدمة، موهبة، أو رحلة">
                    <button class="button primary" type="submit"><x-kabeeri-icon name="search" />Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Trust-first discovery</span>
                <h3>Mall Verification Trust Badge and Moderation UX</h3>
                <p>كل listing مرتبط بفكرة consent، verification، moderation، report، claim، وreputation قبل التحويل أو التواصل.</p>
                <div class="actions" style="margin-top: 14px;">
                    <a class="button" href="{{ route('mall.trust') }}"><x-kabeeri-icon name="trust" />Trust Center</a>
                    <a class="button" href="{{ route('mall.claim-report') }}"><x-kabeeri-icon name="flag" />Claim / Report</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="section-title">
            <div>
                <span class="chip">Mall Home Redesign</span>
                <h2>كل قسم يوضح عدد المنشور ومعنى الثقة.</h2>
            </div>
            <p>{{ __('kabeeri.brand.name') }} Mall لا يخلط نفسه مع {{ __('kabeeri.brand.name') }} Marketplace. هنا زائر يبحث عن أعمال وخدمات وفرص عامة، وليس تثبيت plugin.</p>
        </div>

        <div class="grid-3" aria-label="Mall sections">
            @foreach ($sections as $section)
                <article class="card">
                    <span class="chip">Public listing</span>
                    <h2>
                        <a href="{{ $section['route'] }}">{{ $section['label'] }}</a>
                    </h2>
                    <div class="metric">{{ $section['count'] }}</div>
                    <p>{{ $section['count'] }} published</p>
                    <p>Published with consent, moderation context, claim/report path, and public trust explanation.</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
