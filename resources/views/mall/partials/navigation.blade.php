<nav class="nav" aria-label="Mall sections">
    <a class="{{ request()->routeIs('mall.index') ? 'active' : '' }}" href="{{ route('mall.index') }}"><x-kabeeri-icon name="home" />Mall Home</a>
    <a class="{{ request()->routeIs('mall.search') ? 'active' : '' }}" href="{{ route('mall.search') }}"><x-kabeeri-icon name="search" />Mall Search</a>
    <a class="{{ request()->routeIs('mall.businesses.*') ? 'active' : '' }}" href="{{ route('mall.businesses.index') }}"><x-kabeeri-icon name="store" />Business Directory</a>
    <a class="{{ request()->routeIs('mall.products.*') ? 'active' : '' }}" href="{{ route('mall.products.index') }}"><x-kabeeri-icon name="product" />Products</a>
    <a class="{{ request()->routeIs('mall.services.*') ? 'active' : '' }}" href="{{ route('mall.services.index') }}"><x-kabeeri-icon name="service" />Services</a>
    <a class="{{ request()->routeIs('mall.courses.*') ? 'active' : '' }}" href="{{ route('mall.courses.index') }}"><x-kabeeri-icon name="book" />Courses</a>
    <a class="{{ request()->routeIs('mall.talent.*') ? 'active' : '' }}" href="{{ route('mall.talent.index') }}"><x-kabeeri-icon name="account" />Talent</a>
    <a class="{{ request()->routeIs('mall.travel.*') ? 'active' : '' }}" href="{{ route('mall.travel.index') }}"><x-kabeeri-icon name="map" />Travel</a>
    <a class="{{ request()->routeIs('mall.claim-report') ? 'active' : '' }}" href="{{ route('mall.claim-report') }}"><x-kabeeri-icon name="flag" />Claim / Report</a>
</nav>
