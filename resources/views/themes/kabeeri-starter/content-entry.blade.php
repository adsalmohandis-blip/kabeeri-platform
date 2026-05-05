@extends('themes.kabeeri-starter.layouts.app')

@section('content')
    <article class="ks-card">
        <h1 style="margin: 0 0 12px; font-size: 1.8rem;">{{ $contentEntry->title }}</h1>

        @if($contentEntry->excerpt)
            <p style="margin: 0 0 16px; color: var(--ks-muted);">{{ $contentEntry->excerpt }}</p>
        @endif

        <div>
            {!! nl2br(e($contentEntry->body ?? '')) !!}
        </div>
    </article>
@endsection
