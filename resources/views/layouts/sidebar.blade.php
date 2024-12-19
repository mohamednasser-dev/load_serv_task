<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="javascript:void(0);" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo_icon.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo_whit.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="javascript:void(0);" class="logo logo-light">
            <span class="logo-sm">
                <img style="height: 50px; width: 50px;" src="{{ URL::asset('build/images/logo_icon.png') }}" alt=""
                     height="22">
            </span>
            <span class="logo-lg">
                <img style="height: 62px; width: 170px;" src="{{ URL::asset('build/images/logo_whit.png') }}" alt=""
                     height="37">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>@lang('Dashboard')</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('root') ? 'active' : '' }}" href="/">
                        <i class="ri-home-2-fill"></i> <span>@lang('Home')</span>
                    </a>
                </li>
                @permission('customers_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('customers.*') ? 'active' : '' }}"
                       href="{{ route('customers.index') }}">
                        <i class="ri-team-line"></i> <span>@lang('Customers')</span>
                    </a>
                </li>
                @endpermission
                @permission('products_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('products.*') ? 'active' : '' }}"
                       href="{{ route('products.index') }}">
                        <i class="ri-store-line"></i> <span>@lang('Products')</span>
                    </a>
                </li>
                @endpermission
                @permission('invoices_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('invoices.*') ? 'active' : '' }}"
                       href="{{ route('invoices.index') }}">
                        <i class="ri-file-paper-2-fill"></i> <span>@lang('Invoices')</span>
                    </a>
                </li>
                @endpermission
                @permission('admins_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('admins.*') ? 'active' : '' }}"
                       href="{{ route('admins.index') }}">
                        <i class="ri-user-2-fill"></i> <span>@lang('Admins')</span>
                    </a>
                </li>
                @endpermission
                @permission('employees_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('employees.*') ? 'active' : '' }}"
                       href="{{ route('employees.index') }}">
                        <i class="ri-user-2-fill"></i> <span>@lang('Employees')</span>
                    </a>
                </li>
                @endpermission

                @permission('activity_list')
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('activity_log.*') ? 'active' : '' }}"
                       href="{{ route('activity_log.index') }}">
                        <i class="ri-history-line"></i> <span>@lang('Invoices Logbook')</span>
                    </a>
                </li>
                @endpermission
            </ul>
            <br>
            <br>
            <br>
            <br>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
