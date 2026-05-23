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
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">

                        {{-- Dashboard --}}
                        @auth
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.dashboard'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="las la-home"></i> <span>Dashboards</span>
                            </a>
                        </li>
                        @endif

                        {{-- Master --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.masters'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('crops*','varieties*') ? 'active' : '' }}"
                               href="#sidebarMaster" data-bs-toggle="collapse" role="button">
                                <i class="ri-dashboard-2-line"></i> <span>Master</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('crops*','varieties*') ? 'show' : '' }}" id="sidebarMaster">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('crops.index') }}" class="nav-link {{ request()->routeIs('crops.*') ? 'active' : '' }}">Crop Master</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('categories*','crop-categories*','crop-types*','variety-types*','seasons*','seed-classes*','units*','soil-types*','arrival-types*','pouches*','location*','employees*', 'quality-master*') ? 'active' : '' }}"
                               href="#sidebarMasterSetting" data-bs-toggle="collapse" role="button">
                                <i class="ri-dashboard-2-line"></i> <span>Master Settings</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('categories*','crop-categories*','crop-types*','variety-types*','seasons*','seed-classes*','units*','soil-types*','arrival-types*','pouches*','location*','employees*') ? 'show' : '' }}" id="sidebarMasterSetting">
                                <ul class="nav nav-sm flex-column">
                                  
                                    <li class="nav-item"><a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">Category Master</a></li>
                                    
                                    
                                    <li class="nav-item"><a href="{{ route('crop-categories.index') }}" class="nav-link {{ request()->routeIs('crop-categories.*') ? 'active' : '' }}">Crop Category</a></li>
                                  
                                    <li class="nav-item"><a href="{{ route('crop-types.index') }}" class="nav-link {{ request()->routeIs('crop-types.*') ? 'active' : '' }}">Crop Type</a></li>
                                    
                                    <li class="nav-item"><a href="{{ route('variety-types.index') }}" class="nav-link {{ request()->routeIs('variety-types.*') ? 'active' : '' }}">Variety Type</a></li>
                                  
                                    <li class="nav-item"><a href="{{ route('seasons.index') }}" class="nav-link {{ request()->routeIs('seasons.*') ? 'active' : '' }}">Season</a></li>
                                
                                    <li class="nav-item"><a href="{{ route('seed-classes.index') }}" class="nav-link {{ request()->routeIs('seed-classes.*') ? 'active' : '' }}">Seed Class</a></li>
                                   
                                    <li class="nav-item"><a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">Weight/Capacity Unit</a></li>
                                   
                                    <li class="nav-item"><a href="{{ route('soil-types.index') }}" class="nav-link {{ request()->routeIs('soil-types.*') ? 'active' : '' }}">Soil Type</a></li>
                                   
                                    <li class="nav-item"><a href="{{ route('arrival-types.index') }}" class="nav-link {{ request()->routeIs('arrival-types.*') ? 'active' : '' }}">Arrival Type</a></li>
                                   
                                    <li class="nav-item"><a href="{{ route('pouches.index') }}" class="nav-link {{ request()->routeIs('pouches.*') ? 'active' : '' }}">Pouch Master</a></li>
                                  
                                    <li class="nav-item"><a href="{{ route('location.countries') }}" class="nav-link {{ request()->routeIs('location.*') ? 'active' : '' }}">Location Master</a></li>
                                  
                                    <li class="nav-item"><a href="{{ route('master.employees.index') }}" class="nav-link {{ request()->routeIs('master.employees.*') ? 'active' : '' }}">Employee Master</a></li>
                                    
                                    <li class="nav-item"><a href="{{ route('quality-master.index') }}" class="nav-link {{ request()->routeIs('quality-master.*') ? 'active' : '' }}">Quality Master</a></li>
                                   
                                </ul>
                            </div>
                        </li>
                        @endif

                        {{-- CoreData Sync --}}
                        @if(auth()->user()->hasRole(['super-admin', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('sync*','core_api*') ? 'active' : '' }}"
                               href="#sidebarSync" data-bs-toggle="collapse" role="button">
                                <i class="ri-refresh-line"></i> <span>CoreData Sync</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('sync*','core_api*') ? 'show' : '' }}" id="sidebarSync">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('core_api.*') ? 'active' : '' }}" href="{{ route('core_api.index') }}">Core Api</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif

                        {{-- Accession --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.accession'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('accession.*') ? 'active' : '' }}" href="{{ route('accession.accession-list') }}">
                                <i class="ri-honour-line"></i> <span>Accession List</span>
                            </a>
                        </li>
                        @endif

                        {{-- Lot Management --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.lot'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('lot-management','lot-transfer','warehouse-transfer','lot-types*','lots*') ? 'active' : '' }}"
                               href="#sidebarMasterLot" data-bs-toggle="collapse" role="button">
                                <i class="ri-layout-3-line"></i> <span>Lot / Batch Management</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('lot-management','lot-transfer','warehouse-transfer','lot-types*','lots*') ? 'show' : '' }}" id="sidebarMasterLot">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('lot-management') ? 'active' : '' }}" href="{{ route('lot-management') }}">Lot List</a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('lot-transfer.*') ? 'active' : '' }}" href="{{ route('lot-transfer.index') }}">Lot Inter-Transfer</a></li>
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('storage.transfer'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('warehouse-transfer.*') ? 'active' : '' }}" href="{{ route('warehouse-transfer.index') }}">Warehouse Inter-Transfer</a></li>
                                    @endif
                                    <li class="nav-item"><a class="nav-link" href="{{ route('quality-control.index') }}">Quality Info Update</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('lot-regeneration.index') }}">Lot Regneration</a></li>
                                </ul>
                            </div>
                        </li>
                        
                        @endif

                        {{-- Storage --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.storage'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('storage-management.*','storage-locations*','warehouses*','storage-types*','storage-times*','storage-conditions*','storage-location-master*') ? 'active' : '' }}"
                               href="#sidebarMasterStorage" data-bs-toggle="collapse" role="button">
                                <i class="ri-account-circle-line"></i> <span>Storage Management</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('storage-management','storage-locations*','warehouses*','storage-types*','storage-times*','storage-conditions*','storage-location-master*') ? 'show' : '' }}" id="sidebarMasterStorage">
                                <ul class="nav nav-sm flex-column">
                                  
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('storage-management.*') ? 'active' : '' }}" href="{{ route('storage-management.index') }}">Storage List</a></li>
                               
                                    <li class="nav-item"><a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">Warehouse Master</a></li>
                           
                                    <li class="nav-item"><a href="{{ route('storage-types.index') }}" class="nav-link {{ request()->routeIs('storage-types.*') ? 'active' : '' }}">Storage Type</a></li>
                               
                                    <li class="nav-item"><a href="{{ route('storage-times.index') }}" class="nav-link {{ request()->routeIs('storage-times.*') ? 'active' : '' }}">Storage Time</a></li>
                                 
                                    <li class="nav-item"><a href="{{ route('storage-conditions.index') }}" class="nav-link {{ request()->routeIs('storage-conditions.*') ? 'active' : '' }}">Storage Condition</a></li>
                                 
                                    <li class="nav-item"><a href="{{ route('storage-location-master.index') }}" class="nav-link {{ request()->routeIs('storage-location-master.*') ? 'active' : '' }}">Rack / Bin / Container</a></li>
                                    
                                </ul>
                            </div>
                        </li>
                        @endif

                        {{-- Requests --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.request'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}">
                                <i class="ri-user-3-line"></i> <span>Request List</span>
                            </a>
                        </li>
                        @endif

                        {{-- Dispatch --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.dispatch'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('dispatch-management.*') ? 'active' : '' }}" href="{{ route('dispatch-management.index') }}">
                                <i class="ri-honour-line"></i> <span>Dispatch Management</span>
                            </a>
                        </li>
                        @endif                        

                        {{-- Reports --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.reports'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('report.*', 'report.summary', 'expiry.*', 'dispatch.*') ? 'active' : '' }}" href="{{ route('report.reports') }}" >
                                <i class="ri-file-chart-line"></i> <span>Reports</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->routeIs('report.*', 'report.summary' ,'expiry.*', 'dispatch.*') ? 'show' : '' }}" id="sidebarReport">
                                <ul class="nav nav-sm flex-column">
                                    @if(auth()->user()->hasPermission('report.request'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('report.request') ? 'active' : '' }}" href="{{ route('report.request') }}">Request Report</a></li>
                                    @endif

                                    @if(auth()->user()->hasPermission('report.summary'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('report.summary') ? 'active' : '' }}" href="{{ route('report.summary') }}">Summary Report</a></li>
                                    @endif

                                    @if(auth()->user()->hasPermission('report.expiry'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('expiry.report') ? 'active' : '' }}" href="{{ route('expiry.report') }}">Expiry Report</a></li>
                                    @endif

                                    @if(auth()->user()->hasPermission('report.expiry'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dispatch.report') ? 'active' : '' }}" href="{{ route('dispatch.report') }}">Dispatch Report</a></li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                       
                        @endif

                        {{-- Settings --}}
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.settings'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('settings*','user*','logs*','roles*','permission*') ? 'active' : '' }}"
                               href="#settings" data-bs-toggle="collapse" role="button">
                                <i class="ri-settings-4-line"></i> <span>Settings</span>
                            </a>
                            <div class="collapse menu-visible menu-dropdown {{ request()->is('settings*','user*','logs*','roles*','permission*') ? 'show' : '' }}" id="settings">
                                <ul class="nav nav-sm flex-column">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('settings.roles'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('settings.role') ? 'active' : '' }}" href="{{ route('settings.role') }}">Roles</a></li>
                                    @endif
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('settings.permissions'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('settings.permission') ? 'active' : '' }}" href="{{ route('settings.permission') }}">Permissions</a></li>
                                    @endif
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('settings.users'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">Users</a></li>
                                    @endif
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('menu.logs'))
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="{{ route('logs.index') }}">Log Report</a></li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                        @endif
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
                        @endauth

                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @endif
                        @if (Route::has('register'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @endif
                        @endguest

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->