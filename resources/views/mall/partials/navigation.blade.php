<nav class="nav" aria-label="Mall sections">
    <a class="{{ request()->routeIs('mall.index') ? 'active' : '' }}" href="{{ route('mall.index') }}">Mall Home</a>
    <a class="{{ request()->routeIs('mall.search') ? 'active' : '' }}" href="{{ route('mall.search') }}">Mall Search</a>
    <a class="{{ request()->routeIs('mall.businesses.*') ? 'active' : '' }}" href="{{ route('mall.businesses.index') }}">Business Directory</a>
    <a class="{{ request()->routeIs('mall.products.*') ? 'active' : '' }}" href="{{ route('mall.products.index') }}">Products</a>
    <a class="{{ request()->routeIs('mall.services.*') ? 'active' : '' }}" href="{{ route('mall.services.index') }}">Services</a>
    <a class="{{ request()->routeIs('mall.courses.*') ? 'active' : '' }}" href="{{ route('mall.courses.index') }}">Courses</a>
    <a class="{{ request()->routeIs('mall.talent.*') ? 'active' : '' }}" href="{{ route('mall.talent.index') }}">Talent</a>
    <a class="{{ request()->routeIs('mall.travel.*') ? 'active' : '' }}" href="{{ route('mall.travel.index') }}">Travel</a>
    <a class="{{ request()->routeIs('mall.claim-report') ? 'active' : '' }}" href="{{ route('mall.claim-report') }}">Claim / Report</a>
</nav>
