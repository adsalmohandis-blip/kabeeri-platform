@extends('mall.layout')

@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div>
                <a class="chip" href="{{ route('mall.courses.index') }}">Courses</a>
                <h1>{{ $course->course_name }}</h1>

                @if ($course->training_type)
                    <p>{{ $course->training_type }}</p>
                @endif

                @if ($course->delivery_mode)
                    <p>{{ $course->delivery_mode }}</p>
                @endif

                @if ($course->price)
                    <p>{{ $course->currency }} {{ $course->price }}</p>
                @endif

                @if ($course->description)
                    <p>{{ $course->description }}</p>
                @endif
            </div>
            <aside class="card">
                <span class="chip">Academy-ready</span>
                <h3>Course trust</h3>
                <p>Course pages can show instructor info, schedule, delivery mode, and Academy badge relation as the network grows.</p>
            </aside>
        </div>
    </section>
@endsection
