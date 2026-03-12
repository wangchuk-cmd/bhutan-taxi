<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Bhutan Intercity Taxi Booking System - Book your ride across dzongkhags">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    
    <title>@yield('title', 'Bhutan Taxi') - Intercity Taxi Booking</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Mobile App Styles -->
    <link href="{{ asset('css/mobile-app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home-professional.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <i class="bi bi-taxi-front-fill me-2"></i>
                Bhutan Taxi
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-fill me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ request()->routeIs('home') ? route('home')."#search-section" : route('search')."#passenger-results-search-form" }}">
                            <i class="bi bi-search me-1"></i> Search Trips
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Passenger Registration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="{{ route('driver.register') }}">
                                <i class="bi bi-car-front me-1"></i> Driver Registration
                            </a>
                        </li>
                        <!-- Hidden Admin Login Link, revealed by keyboard shortcut -->
                        <li class="nav-item d-none" id="admin-login-nav">
                            <a class="nav-link text-danger" href="{{ route('admin.login') }}">
                                <i class="bi bi-shield-lock me-1"></i> Admin Login
                            </a>
                        </li>
                        <script>
                        // Reveal Admin Login link when user double-presses 'A' key
                        let lastKeyTime = 0;
                        document.addEventListener('keydown', function(e) {
                            if (e.key.toLowerCase() === 'a') {
                                const now = Date.now();
                                if (now - lastKeyTime < 400) {
                                    document.getElementById('admin-login-nav').classList.remove('d-none');
                                }
                                lastKeyTime = now;
                            }
                        });
                        </script>
                    @else
                        <li class="nav-item position-relative">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell-fill me-1"></i>
                                @php
                                    $unreadCount = auth()->user()->notifications()->unread()->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin Dashboard
                                </a>
                            </li>
                        @elseif(auth()->user()->isDriver())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('driver.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Driver Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bookings.my') }}">
                                    <i class="bi bi-ticket-perforated me-1"></i> My Bookings
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('feedback') }}">
                                        <i class="bi bi-chat-dots me-2"></i> Feedback
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    
    <!-- Main Content -->
    @yield('content')
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="bi bi-taxi-front-fill me-2"></i>Bhutan Taxi</h5>
                    <p class="text-muted">Your trusted intercity taxi booking platform connecting passengers with drivers across all dzongkhags of Bhutan.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="{{ request()->routeIs('home') ? route('home')."#search-section" : route('search')."#passenger-results-search-form" }}" class="text-muted text-decoration-none">Search Trips</a></li>
                        <li><a href="{{ route('driver.register') }}" class="text-muted text-decoration-none">Become a Driver</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contact</h6>
                    <p class="text-muted mb-1"><i class="bi bi-envelope me-2"></i>support@bhutantaxi.bt</p>
                    <p class="text-muted mb-1"><i class="bi bi-telephone me-2"></i>+975-17-123456</p>
                    <p class="text-muted"><i class="bi bi-geo-alt me-2"></i>Thimphu, Bhutan</p>
                </div>
            </div>
            <hr class="my-3 bg-secondary">
            <div class="text-center text-muted">
                <small>&copy; {{ date('Y') }} Bhutan Taxi Booking System. All rights reserved.</small>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-lg-none">
        <div class="nav">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="bi bi-house-fill"></i>
                <span>Home</span>
            </a>
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}#search-section">
                <i class="bi bi-search"></i>
                <span>Search</span>
            </a>
                <!-- Scroll/focus search form if anchor present -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const hash = window.location.hash;
                    if (hash === '#search-section' || hash === '#passenger-results-search-form') {
                        const el = document.querySelector(hash);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            // Try to focus first input in form/card
                            const input = el.querySelector('input, select, textarea');
                            if (input) input.focus();
                        }
                    }
                });
                </script>
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Admin</span>
                    </a>
                @elseif(auth()->user()->isDriver())
                    <a class="nav-link {{ request()->routeIs('driver.*') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                        <i class="bi bi-car-front"></i>
                        <span>Driver</span>
                    </a>
                @else
                    <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.my') }}">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>Bookings</span>
                    </a>
                @endif
                <a class="nav-link position-relative {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell-fill"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                    <span>Alerts</span>
                </a>
            @else
                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
                <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Passenger Register</span>
                </a>
                <a class="nav-link {{ request()->routeIs('driver.register') ? 'active' : '' }}" href="{{ route('driver.register') }}">
                    <i class="bi bi-car-front"></i>
                    <span>Driver Register</span>
                </a>
            @endauth
        </div>
    </nav>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dzongkhag Autocomplete -->
    <script src="{{ asset('js/dzongkhag-autocomplete.js') }}"></script>
    
    <!-- Real-time Updates -->
    <script src="{{ asset('js/realtime-updates.js') }}"></script>
    
    <!-- Phone Number Validation -->
    <script src="{{ asset('js/phone-validation.js') }}"></script>
    
    <!-- Form Submit Debug -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Log form submissions
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitting:', form.id || form.action);
                console.log('Form data:', new FormData(form));
            });
        });
    });
    </script>
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
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

        // Hardening for passenger search date fields.
        document.addEventListener('DOMContentLoaded', function () {
            const toLocalDateString = () => {
                const now = new Date();
                const offsetMs = now.getTimezoneOffset() * 60000;
                return new Date(now.getTime() - offsetMs).toISOString().slice(0, 10);
            };

            const pairs = [
                ['home-search-form', 'home-search-date'],
                ['passenger-results-search-form', 'passenger-results-search-date'],
            ];

            pairs.forEach(([formId, inputId]) => {
                const form = document.getElementById(formId);
                const dateInput = document.getElementById(inputId);
                if (!form || !dateInput) return;

                if (!dateInput.value) {
                    dateInput.value = toLocalDateString();
                }

                form.addEventListener('submit', function () {
                    if (!dateInput.value) {
                        dateInput.value = toLocalDateString();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
