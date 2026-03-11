<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>@yield('title', 'Admin Dashboard') - Bhutan Taxi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/mobile-app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mobile-responsive.css') }}" rel="stylesheet">
    @stack('styles')
    
    <style>
        /* Admin Panel specific overrides */
        .navbar {
            background: var(--dark) !important;
        }
        .sidebar-dark {
            background: var(--dark);
        }
        .sidebar-dark .nav-link {
            color: rgba(255,255,255,0.7);
        }
        .sidebar-dark .nav-link:hover,
        .sidebar-dark .nav-link.active {
            background: var(--primary);
            color: white;
        }
        .sidebar-dark .nav-section {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 1rem 0.5rem;
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
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check me-2"></i>
                <span class="d-none d-sm-inline">Admin Panel</span>
                <span class="d-sm-none">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-sm btn-outline-light d-none d-lg-inline-flex" href="{{ route('home') }}">
                    <i class="bi bi-house me-1"></i>Main Site
                </a>
                <div class="dropdown">
                    <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline ms-1">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item d-lg-none" href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Main Site</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
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
        <div class="sidebar sidebar-dark d-none d-lg-block" style="position: fixed; width: 220px; top: 56px; height: calc(100vh - 56px); overflow-y: auto;">
            <nav class="nav flex-column py-3">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                
                <div class="nav-section">Management</div>
                <a class="nav-link {{ request()->routeIs('admin.routes*') ? 'active' : '' }}" href="{{ route('admin.routes') }}">
                    <i class="bi bi-signpost-2 me-2"></i>Routes
                </a>
                <a class="nav-link {{ request()->routeIs('admin.drivers*') ? 'active' : '' }}" href="{{ route('admin.drivers') }}">
                    <i class="bi bi-person-badge me-2"></i>Drivers
                </a>
                <a class="nav-link {{ request()->routeIs('admin.trips*') ? 'active' : '' }}" href="{{ route('admin.trips') }}">
                    <i class="bi bi-map me-2"></i>Trips
                </a>
                <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings') }}">
                    <i class="bi bi-ticket-perforated me-2"></i>Bookings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                    <i class="bi bi-people me-2"></i>Users
                </a>
                
                <div class="nav-section">Finance</div>
                <a class="nav-link {{ request()->routeIs('admin.payouts*') ? 'active' : '' }}" href="{{ route('admin.payouts') }}">
                    <i class="bi bi-wallet2 me-2"></i>Payouts
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                    <i class="bi bi-graph-up me-2"></i>Reports
                </a>
                
                <div class="nav-section">Support</div>
                <a class="nav-link {{ request()->routeIs('admin.complaints*') ? 'active' : '' }}" href="{{ route('admin.complaints') }}">
                    <i class="bi bi-chat-dots me-2"></i>Complaints
                </a>
                
                <div class="nav-section">System</div>
                <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                    <i class="bi bi-gear me-2"></i>Settings
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
            
            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation for Admin -->
    <nav class="mobile-bottom-nav d-lg-none">
        <div class="nav">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings') }}">
                <i class="bi bi-ticket-perforated"></i>
                <span>Bookings</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.trips*') ? 'active' : '' }}" href="{{ route('admin.trips') }}">
                <i class="bi bi-map"></i>
                <span>Trips</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.drivers*') ? 'active' : '' }}" href="{{ route('admin.drivers') }}">
                <i class="bi bi-person-badge"></i>
                <span>Drivers</span>
            </a>
            <a class="nav-link" href="#" onclick="toggleMobileMenu(event)">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>More</span>
            </a>
        </div>
    </nav>

    <!-- Mobile Full Menu Modal -->
    <div class="modal fade" id="mobileMenuModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-grid me-2"></i>Admin Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-light py-2 fw-bold small text-uppercase text-muted">Management</div>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.routes') }}">
                            <i class="bi bi-signpost-2 me-3 text-primary"></i>Routes
                        </a>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.users') }}">
                            <i class="bi bi-people me-3 text-primary"></i>Users
                        </a>
                        <div class="list-group-item bg-light py-2 fw-bold small text-uppercase text-muted">Finance</div>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.payouts') }}">
                            <i class="bi bi-wallet2 me-3 text-success"></i>Payouts
                        </a>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.reports') }}">
                            <i class="bi bi-graph-up me-3 text-success"></i>Reports
                        </a>
                        <div class="list-group-item bg-light py-2 fw-bold small text-uppercase text-muted">Support</div>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.complaints') }}">
                            <i class="bi bi-chat-dots me-3 text-warning"></i>Complaints
                        </a>
                        <div class="list-group-item bg-light py-2 fw-bold small text-uppercase text-muted">System</div>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('admin.settings') }}">
                            <i class="bi bi-gear me-3 text-secondary"></i>Settings
                        </a>
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('home') }}">
                            <i class="bi bi-house me-3 text-info"></i>Main Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dzongkhag-autocomplete.js') }}"></script>
    <script src="{{ asset('js/realtime-updates.js') }}"></script>
    <script src="{{ asset('js/phone-validation.js') }}"></script>
    <script src="{{ asset('js/mobile-responsive.js') }}"></script>
    
    <!-- Form Submit Debug -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitting:', form.id || form.action);
            });
        });
    });
    </script>
    
    <script>
        // Mobile menu toggle
        function toggleMobileMenu(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('mobileMenuModal'));
            modal.show();
        }

        // Normalize search date inputs to browser-local date to avoid HTML5 min-date loop.
        document.addEventListener('DOMContentLoaded', function () {
            const toLocalDateString = () => {
                const now = new Date();
                const offsetMs = now.getTimezoneOffset() * 60000;
                return new Date(now.getTime() - offsetMs).toISOString().slice(0, 10);
            };

            const localToday = toLocalDateString();
            const searchForms = document.querySelectorAll('form[action*="search"]');

            searchForms.forEach((form) => {
                const dateInput = form.querySelector('input[type="date"][name="date"]');
                if (!dateInput) return;

                dateInput.min = localToday;

                // If server-rendered value is missing/invalid against local min, reset to today.
                if (!dateInput.value || dateInput.value < localToday) {
                    dateInput.value = localToday;
                }

                form.addEventListener('submit', function () {
                    dateInput.min = toLocalDateString();
                    if (!dateInput.value || dateInput.value < dateInput.min) {
                        dateInput.value = dateInput.min;
                    }
                });
            });
        });

        // Hardening for admin booking search date field.
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('admin-booking-search-form');
            const dateInput = document.getElementById('admin-booking-search-date');
            if (!form || !dateInput) return;

            const toLocalDateString = () => {
                const now = new Date();
                const offsetMs = now.getTimezoneOffset() * 60000;
                return new Date(now.getTime() - offsetMs).toISOString().slice(0, 10);
            };

            if (!dateInput.value) {
                dateInput.value = toLocalDateString();
            }

            form.addEventListener('submit', function () {
                if (!dateInput.value) {
                    dateInput.value = toLocalDateString();
                }
            });
        });
        
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
