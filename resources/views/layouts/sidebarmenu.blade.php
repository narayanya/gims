<!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO Vertical-->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('home') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm-w.png') }}" alt="" style="height: 40px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo300x300.png') }}" alt="" style="height: 70px" >
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('home') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm-w.png') }}" alt="" style="height: 50px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo300x300.png') }}" alt="" style="height: 40px; margin-top: 10px" >
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
    
       
            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" aria-expanded="false">
                                <i class="las la-home"></i> <span data-key="t-dashboards">Dashboards</span>
                            </a>
                        </li> <!-- end Dashboard Menu -->

                        @auth
                            @if(auth()->user()->hasRole(['super-admin', 'admin', 'manager']))
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->is('crops*','varieties*') ? 'active' : '' }}" href="#sidebarMaster" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMaster">
                                        <i class="ri-dashboard-2-line"></i> <span data-key="t-master">Master</span>
                                    </a>
                                    <div class="collapse menu-dropdown {{ request()->is('crops*','varieties*') ? 'show' : '' }}" id="sidebarMaster" style="visibility: visible;">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{ route('crops.index') }}" class="nav-link {{ request()->routeIs('crops.*') ? 'active' : '' }}" data-key="t-analytics"> Crop Master </a>
                                            </li>
                                            <li class="nav-item d-none">
                                                <a href="{{ route('varieties.index') }}" class="nav-link {{ request()->routeIs('varieties.*') ? 'active' : '' }}" data-key="t-crm"> Variety/Seed Master </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <li class="nav-item" >
                                    <a class="nav-link menu-link 
                                        {{ request()->is(
                                            'categories*',
                                            'crop-categories*',
                                            'crop-types*',
                                            'variety-types*',
                                            'seasons*',
                                            'seed-classes*',
                                            'units*',
                                            'soil-types*'
                                        ) ? 'active' : '' }}"
                                        
                                        href="#sidebarMasterSetting"
                                        data-bs-toggle="collapse"
                                        role="button">

                                        <i class="ri-dashboard-2-line"></i>
                                        <span>Master Settings</span>
                                    </a>

                                    <div class="collapse menu-dropdown 
                                        {{ request()->is(
                                            'categories*',
                                            'crop-categories*',
                                            'crop-types*',
                                            'variety-types*',
                                            'seasons*',
                                            'seed-classes*',
                                            'units*',
                                            
                                            'soil-types*',
                                            
                                            'location*'
                                        ) ? 'show' : '' }}"
                                        
                                        id="sidebarMasterSetting" style="visibility: visible;">

                                        <ul class="nav nav-sm flex-column">

                                            <li class="nav-item">
                                                <a href="{{ route('categories.index') }}"
                                                class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                                Category Master
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('crop-categories.index') }}"
                                                class="nav-link {{ request()->routeIs('crop-categories.*') ? 'active' : '' }}">
                                                Crop Category
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('crop-types.index') }}"
                                                class="nav-link {{ request()->routeIs('crop-types.*') ? 'active' : '' }}">
                                                Crop Type
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('variety-types.index') }}"
                                                class="nav-link {{ request()->routeIs('variety-types.*') ? 'active' : '' }}">
                                                Variety Type
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('seasons.index') }}"
                                                class="nav-link {{ request()->routeIs('seasons.*') ? 'active' : '' }}">
                                                Season
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('seed-classes.index') }}"
                                                class="nav-link {{ request()->routeIs('seed-classes.*') ? 'active' : '' }}">
                                                Seed Class
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('units.index') }}"
                                                class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                                                Weight/Capacity Unit Master
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('soil-types.index') }}"
                                                class="nav-link {{ request()->routeIs('soil-types.*') ? 'active' : '' }}">
                                                Soil Type
                                                </a>
                                            </li>
                                            
                                            <li class="nav-item">
                                                <a href="{{ route('location.countries') }}"
                                                class="nav-link {{ request()->routeIs('location.*') ? 'active' : '' }}">
                                                Location Master
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('master.employees.index') }}"
                                                class="nav-link {{ request()->routeIs('master.employees.*') ? 'active' : '' }}">
                                                Employee Master
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link menu-link 
                                        {{ request()->is('sync*') ? 'active' : '' }}"
                                        
                                        href="#sidebarSync"
                                        data-bs-toggle="collapse"
                                        role="button">

                                        <i class="ri-refresh-line"></i>
                                        <span>CoreData Sync</span>
                                    </a>

                                    <div class="collapse menu-dropdown 
                                        {{ request()->is('sync*') ? 'show' : '' }}"
                                        
                                        id="sidebarSync" style="visibility: visible;">

                                        <ul class="nav nav-sm flex-column">

                                            {{-- Future use --}}
                                            {{-- 
                                            <li class="nav-item">
                                                <a href="{{ route('sync.index') }}"
                                                class="nav-link {{ request()->routeIs('sync.*') ? 'active' : '' }}">
                                                Master Data Sync
                                                </a>
                                            </li>
                                            --}}

                                            {{--<li class="nav-item">
                                                <a href="{{ route('sync.location.index') }}"
                                                class="nav-link {{ request()->routeIs('sync.location.*') ? 'active' : '' }}">
                                                Location Sync
                                                </a>
                                            </li>--}}
                                            <li class="nav-item">
                                                <a class="nav-link menu-link @activeRoute('core_api.*')" href="{{ route('core_api.index') }}">
                                                    Core Api
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->is('lot-management', 'lot-types' ,'lots*') ? 'active' : '' }} "  href="#sidebarMasterLot"
                                        data-bs-toggle="collapse"
                                        role="button">
                                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">Lot / Batch Management</span>
                                    </a>
                                    <div class="collapse menu-dropdown 
                                        {{ request()->is(
                                            'lot-management',
                                            'lot-types',
                                            'lots*'
                                        ) ? 'show' : '' }}"
                                        
                                        id="sidebarMasterLot" style="visibility: visible;">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a class="nav-link menu-link {{ request()->routeIs('lot-management') ? 'active' : '' }}" href="{{ route('lot-management') }}" >
                                                    Lot List
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link menu-link {{ request()->routeIs('lot-transfer.*') ? 'active' : '' }}" href="{{ route('lot-transfer.index') }}" >
                                                    Lot Inter-Transfer
                                                </a>
                                            </li>
                                            <li class="nav-item d-none">
                                                <a href="{{ route('lots.index') }}" class="nav-link {{ request()->routeIs('lots.*') ? 'active' : '' }}">
                                                Lot Master
                                                </a>
                                            </li>
                                            <li class="nav-item d-none">
                                                <a href="{{ route('lot-types.index') }}" class="nav-link {{ request()->routeIs('lot-types.*') ? 'active' : '' }}">
                                                Lot Type
                                                </a>
                                            </li> 
                                                                                       
                                        </ul>
                                    </div>
                                </li>

                                <li class="nav-item">
                                     <a class="nav-link menu-link {{ request()->routeIs('storage-management.*', 'storage-locations*', 'warehouses*',
                                            'storage-types*',
                                            'storage-times*',
                                            'storage-conditions*',
                                            'storage-location-master*'
                                            ) ? 'active' : '' }}" href="#sidebarMasterStorage" data-bs-toggle="collapse"
                                        role="button">
                                        <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Storage Management</span>
                                    </a>
                                    <div class="collapse menu-dropdown 
                                        {{ request()->is(
                                            'storage-management',
                                            'storage-locations*',
                                            'warehouses*',
                                            'storage-types*',
                                            'storage-times*',
                                            'storage-conditions*',
                                            'storage-location-master*'
                                        ) ? 'show' : '' }}"
                                        
                                        id="sidebarMasterStorage" style="visibility: visible;">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                 <a class="nav-link menu-link {{ request()->routeIs('storage-management.*') ? 'active' : '' }}" href="{{ route('storage-management.index') }}" >
                                                    Storage list
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" data-key="t-projects"> Warehouse Master </a>
                                            </li>
                                            {{--<li class="nav-item">
                                                <a href="{{ route('storage-locations.index') }}"
                                                class="nav-link {{ request()->routeIs('storage-locations.*') ? 'active' : '' }}">
                                                Storage Location
                                                </a>
                                            </li>--}}

                                            <li class="nav-item">
                                                <a href="{{ route('storage-types.index') }}"
                                                class="nav-link {{ request()->routeIs('storage-types.*') ? 'active' : '' }}">
                                                Storage Type
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('storage-times.index') }}"
                                                class="nav-link {{ request()->routeIs('storage-times.*') ? 'active' : '' }}">
                                                Storage Time
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{ route('storage-conditions.index') }}"
                                                class="nav-link {{ request()->routeIs('storage-conditions.*') ? 'active' : '' }}">
                                                Storage Condition
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('storage-location-master.index') }}"
                                                class="nav-link {{ request()->routeIs('storage-location-master.*') ? 'active' : '' }}">
                                                Section / Rack / Bin / Container
                                                </a>
                                            </li>
                                            

                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('dispatch-management.*') ? 'active' : '' }}" href="{{ route('dispatch-management.index') }}">
                                        <i class="ri-honour-line"></i> <span data-key="t-widgets">Dispatch Management</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Accession List - Available to all authenticated users -->
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('accession.*') ? 'active' : '' }}" href="{{ route('accession.accession-list') }}">
                                    <i class="ri-honour-line"></i> <span data-key="t-widgets">Accession List</span>
                                </a>
                            </li>

                            @if(auth()->user()->hasRole(['super-admin', 'admin', 'manager']))
                                <li class="nav-item d-none">
                                    <a class="nav-link menu-link {{ request()->routeIs('accession-rules.*') ? 'active' : '' }}" href="{{ route('accession-rules.index') }}">
                                        <i class="ri-honour-line"></i> <span data-key="t-widgets">Accession Rule</span>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('report.*') ? 'active' : '' }}" href="{{ route('report.reports') }}">
                                        <i class="ri-file-chart-line"></i> <span data-key="t-widgets">Reports</span>
                                    </a>
                                </li>
                            @endif

                            <!-- User Management - Only Super Admin and Admin -->
                            @if(auth()->user()->hasRole(['super-admin', 'admin']))
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">
                                        <i class="ri-user-3-line"></i> <span data-key="t-widgets">Users</span>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->hasRole(['super-admin', 'admin', 'manager','user']))
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}">
                                        <i class="ri-user-3-line"></i> <span data-key="t-widgets">Request List</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Settings - Only Super Admin -->
                            @if(auth()->user()->hasRole('super-admin'))
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}">
                                        <i class="ri-settings-4-line"></i> <span data-key="t-widgets">Settings</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Settings - Only Super Admin -->
                            @if(auth()->user()->hasRole('super-admin'))
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="{{ route('logs.index') }}">
                                        <i class="ri-file-list-3-line"></i> <span data-key="t-widgets">Log Report</span>
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @guest

                        @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                        <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                       <i class="mdi mdi-logout"></i> <span data-key="t-maps">{{ __('Logout') }}</span>
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                            </li>
                        @endguest
                        
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->