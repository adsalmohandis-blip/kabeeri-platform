@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Courses</span>
                <h1>Courses</h1>
                <p>Public learning offers connected to training type, delivery mode, Academy signals, and moderation.</p>
                <form class="search" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search courses">
                    <button class="button primary" type="submit">Search</button>
                </form>
            </div>
            <aside class="card">
                <span class="chip">Course Index and Detail Upgrade</span>
                <h3>Learn with context</h3>
                <p>Courses can later connect to Academy badges, instructor info, reviews, and safe inquiry paths.</p>
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="grid-3">
            @forelse ($courses as $course)
                <article class="card">
                    <span class="chip">{{ $course->training_type ?: 'Course' }}</span>
                    <h2>
                        <a href="{{ route('mall.courses.show', $course) }}">{{ $course->course_name }}</a>
                    </h2>

                    @if ($course->training_type)
                        <p>{{ $course->training_type }}</p>
                    @endif

                    @if ($course->description)
                        <p>{{ $course->description }}</p>
                    @endif
                </article>
            @empty
                <p>No courses are published yet.</p>
            @endforelse
        </div>

        {{ $courses->links() }}
    </section>
@endsection
