<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f97316">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>@yield('title', 'Driver Dashboard') - Bhutan Taxi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/mobile-app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    @stack('styles')
    
    <style>
        /* Driver Portal specific overrides */
        .navbar {
            background: var(--dark) !important;
        }
        .sidebar {
            background: var(--dark);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--primary);
            color: white;
        }
        .mobile-bottom-nav .nav-link.active {
            color: var(--primary);
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="{{ route('driver.dashboard') }}">
                <i class="bi bi-car-front-fill me-2"></i>
                <span class="d-none d-sm-inline">Driver Portal</span>
                <span class="d-sm-none">Driver</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-sm btn-outline-light d-none d-lg-inline-flex" href="{{ route('home') }}">
                    <i class="bi bi-house me-1"></i>Main Site
                </a>
                <a class="nav-link text-white position-relative d-none d-lg-flex" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell-fill"></i>
                    @php $unread = auth()->user()->notifications()->unread()->count(); @endphp
                    @if($unread > 0)
                        <span class="notification-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
                <div class="dropdown">
                    <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline ms-1">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('driver.profile') }}"><i class="bi bi-gear me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item d-lg-none" href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Main Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Desktop Sidebar -->
        <div class="sidebar d-none d-lg-block" style="position: fixed; width: 220px; top: 56px; height: calc(100vh - 56px);">
            <nav class="nav flex-column py-3">
                <a class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('driver.trips*') ? 'active' : '' }}" href="{{ route('driver.trips') }}">
                    <i class="bi bi-map me-2"></i>My Trips
                </a>
                <a class="nav-link {{ request()->routeIs('driver.payouts') ? 'active' : '' }}" href="{{ route('driver.payouts') }}">
                    <i class="bi bi-wallet2 me-2"></i>Payouts
                </a>
                <a class="nav-link {{ request()->routeIs('driver.profile') ? 'active' : '' }}" href="{{ route('driver.profile') }}">
                    <i class="bi bi-person me-2"></i>Profile
                </a>
                <a class="nav-link {{ request()->routeIs('driver.feedback*') ? 'active' : '' }}" href="{{ route('driver.feedback') }}">
                    <i class="bi bi-chat-square-text me-2"></i>Feedback
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1" style="margin-left: 220px; padding: 1rem;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-info-circle me-2"></i>{{ session('info') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation for Driver -->
    <nav class="mobile-bottom-nav d-lg-none">
        <div class="nav">
            <a class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Home</span>
            </a>
            <a class="nav-link {{ request()->routeIs('driver.trips*') ? 'active' : '' }}" href="{{ route('driver.trips') }}">
                <i class="bi bi-map"></i>
                <span>Trips</span>
            </a>
            <a class="nav-link {{ request()->routeIs('driver.trips.create') ? 'active' : '' }}" href="{{ route('driver.trips.create') }}" style="margin-top: -15px;">
                <div style="background: var(--secondary); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(249,115,22,0.4);">
                    <i class="bi bi-plus-lg" style="font-size: 1.5rem;"></i>
                </div>
                <span style="margin-top: 5px;">New Trip</span>
            </a>
            <a class="nav-link {{ request()->routeIs('driver.payouts') ? 'active' : '' }}" href="{{ route('driver.payouts') }}">
                <i class="bi bi-wallet2"></i>
                <span>Payouts</span>
            </a>
            <a class="nav-link position-relative {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                <i class="bi bi-bell"></i>
                @if($unread > 0)
                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </span>
                @endif
                <span>Alerts</span>
            </a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dzongkhag-autocomplete.js') }}"></script>
    <script src="{{ asset('js/realtime-updates.js') }}"></script>
    <script src="{{ asset('js/phone-validation.js') }}"></script>
    
    <script>
        // Adjust main content margin on mobile
        function adjustLayout() {
            const mainContent = document.querySelector('.main-content');
            if (window.innerWidth < 992) {
                mainContent.style.marginLeft = '0';
                mainContent.style.paddingBottom = '80px';
            } else {
                mainContent.style.marginLeft = '220px';
                mainContent.style.paddingBottom = '1rem';
            }
        }
        adjustLayout();
        window.addEventListener('resize', adjustLayout);
    </script>
    @stack('scripts')
</body>
</html>
