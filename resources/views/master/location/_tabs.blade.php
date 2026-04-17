<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $active === 'countries' ? 'active' : '' }}" href="{{ route('location.countries') }}">
            <i class="ri-earth-line me-1"></i> Countries
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $active === 'states' ? 'active' : '' }}" href="{{ route('location.states') }}">
            <i class="ri-map-2-line me-1"></i> States
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $active === 'districts' ? 'active' : '' }}" href="{{ route('location.districts') }}">
            <i class="ri-map-pin-2-line me-1"></i> Districts
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $active === 'cities' ? 'active' : '' }}" href="{{ route('location.cities') }}">
            <i class="ri-building-2-line me-1"></i> Cities
        </a>
    </li>
</ul>
