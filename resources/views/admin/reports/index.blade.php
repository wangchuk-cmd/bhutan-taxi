@extends('layouts.admin')

@section('title', 'Reports & Data Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-earmark-spreadsheet me-2"></i>Reports & Data Management</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Search, Edit & Export Data</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="exportTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tripsTab">
                        <i class="bi bi-car-front me-1"></i>Trips
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bookingsTab">
                        <i class="bi bi-calendar-check me-1"></i>Bookings
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#refundsTab">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Refunds
                        <span class="badge bg-danger ms-1" id="refundsBadge">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#paymentsTab">
                        <i class="bi bi-credit-card me-1"></i>Payments
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#driversTab">
                        <i class="bi bi-person-badge me-1"></i>Drivers
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#payoutsTab">
                        <i class="bi bi-wallet2 me-1"></i>Payouts
                    </button>
                </li>
            </ul>

            <div class="alert alert-info alert-sm py-2 small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Date Filter Tips:</strong> 
                Enter only <strong>Date From</strong> to search that exact date. 
                Fill both <strong>Date From</strong> and <strong>Date To</strong> for a range. 
                Use <strong>Days</strong> field (e.g., 7,9,10) to search specific non-consecutive days in the month.
            </div>

            <div class="tab-content">
                <!-- Trips Tab -->
                <div class="tab-pane fade show active" id="tripsTab">
                    <form id="tripsFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small" title="e.g. 7,9,10">Days</label>
                                <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Origin</label>
                                <select name="origin" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Destination</label>
                                <select name="destination" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchTrips()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Driver</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                    <th>Price/Seat</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tripsTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Click Search to load data</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="tripsCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportTrips()">
                            <i class="bi bi-download me-2"></i>Export to CSV
                        </button>
                    </div>
                </div>

                <!-- Bookings Tab -->
                <div class="tab-pane fade" id="bookingsTab">
                    <form id="bookingsFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small" title="e.g. 7,9,10">Days</label>
                                <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Payment</label>
                                <select name="payment_status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name/Phone">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchBookings()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Passenger</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <tr><td colspan="9" class="text-center text-muted">Click Search to load data</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="bookingsCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportBookings()">
                            <i class="bi bi-download me-2"></i>Export to CSV
                        </button>
                    </div>
                </div>

                <!-- Refunds Tab -->
                <div class="tab-pane fade" id="refundsTab">
                    <form id="refundsFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small" title="e.g. 7,9,10">Days</label>
                                <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Refund Status</label>
                                <select name="refund_status" class="form-select form-select-sm">
                                    <option value="">All Refund Requests</option>
                                    <option value="pending" selected>Pending</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name/Phone/Booking ID">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchRefunds()">
                                    <i class="bi bi-search me-1"></i>Search Refunds
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Passenger</th>
                                    <th>Phone</th>
                                    <th>Route</th>
                                    <th>Amount</th>
                                    <th>Cancelled</th>
                                    <th>Refund Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="refundsTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Click Search to load refund requests</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="refundsCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportRefunds()">
                            <i class="bi bi-download me-2"></i>Export Refunds CSV
                        </button>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div class="tab-pane fade" id="paymentsTab">
                    <form id="paymentsFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small" title="e.g. 7,9,10">Days</label>
                                <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Method</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="mbob">mBoB</option>
                                    <option value="bnb">BNB</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Txn ID">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchPayments()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Booking</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Txn ID</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Click Search to load data</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="paymentsCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportPayments()">
                            <i class="bi bi-download me-2"></i>Export to CSV
                        </button>
                    </div>
                </div>

                <!-- Drivers Tab -->
                <div class="tab-pane fade" id="driversTab">
                    <form id="driversFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Verified</label>
                                <select name="verified" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="1">Verified</option>
                                    <option value="0">Unverified</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Active</label>
                                <select name="active" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Vehicle</label>
                                <select name="vehicle_type" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="sedan">Sedan</option>
                                    <option value="suv">SUV</option>
                                    <option value="van">Van</option>
                                    <option value="hiace">Hiace</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name/Phone/License">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchDrivers()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>License</th>
                                    <th>Vehicle</th>
                                    <th>Verified</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="driversTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Click Search to load data</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="driversCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportDrivers()">
                            <i class="bi bi-download me-2"></i>Export to CSV
                        </button>
                    </div>
                </div>

                <!-- Payouts Tab -->
                <div class="tab-pane fade" id="payoutsTab">
                    <form id="payoutsFilterForm" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small" title="e.g. 7,9,10">Days</label>
                                <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Driver</label>
                                <select name="driver_id" class="form-select form-select-sm">
                                    <option value="">All Drivers</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->user->name ?? 'Unknown' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchPayouts()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Driver</th>
                                    <th>Trip</th>
                                    <th>Total</th>
                                    <th>Charge</th>
                                    <th>Payout</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="payoutsTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Click Search to load data</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span id="payoutsCount" class="text-muted small"></span>
                        <button type="button" class="btn btn-success" onclick="exportPayouts()">
                            <i class="bi bi-download me-2"></i>Export to CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Edit Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn" onclick="saveEdit()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentEditType = '';
    let currentEditId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadPendingRefundsCount();
    });

    function loadPendingRefundsCount() {
        fetch(`{{ route('admin.reports.search.refunds') }}?refund_status=pending`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('refundsBadge').textContent = data.length;
            });
    }

    function getFormData(formId) {
        const form = document.getElementById(formId);
        const data = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of data.entries()) {
            if (value) params.append(key, value);
        }
        return params.toString();
    }

    function searchTrips() {
        const params = getFormData('tripsFilterForm');
        fetch(`{{ route('admin.reports.search.trips') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tripsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No trips found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(t => `
                        <tr>
                            <td>${t.id}</td>
                            <td>${t.driver_name}</td>
                            <td>${t.origin} → ${t.destination}</td>
                            <td>${t.departure}</td>
                            <td>${t.available_seats}/${t.total_seats}</td>
                            <td>Nu. ${t.price_per_seat}</td>
                            <td><span class="badge bg-${t.status === 'active' ? 'success' : t.status === 'completed' ? 'primary' : 'secondary'}">${t.status}</span></td>
                            <td>
                                <a href="/admin/trips/${t.id}/edit" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    `).join('');
                }
                document.getElementById('tripsCount').textContent = `${data.length} records found`;
            });
    }

    function searchBookings() {
        const params = getFormData('bookingsFilterForm');
        fetch(`{{ route('admin.reports.search.bookings') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('bookingsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No bookings found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(b => `
                        <tr>
                            <td>${b.id}</td>
                            <td>${b.passenger_name}<br><small class="text-muted">${b.passenger_phone}</small></td>
                            <td>${b.route}</td>
                            <td>${b.departure}</td>
                            <td>${b.seats}</td>
                            <td>Nu. ${b.amount}</td>
                            <td><span class="badge bg-${b.status === 'confirmed' ? 'success' : b.status === 'completed' ? 'primary' : 'secondary'}">${b.status}</span></td>
                            <td><span class="badge bg-${b.payment_status === 'paid' ? 'success' : 'warning'}">${b.payment_status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editBooking(${b.id}, '${b.status}', '${b.payment_status}')" title="Edit"><i class="bi bi-pencil"></i></button>
                                <a href="/admin/bookings/${b.id}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    `).join('');
                }
                document.getElementById('bookingsCount').textContent = `${data.length} records found`;
            });
    }

    function searchRefunds() {
        const params = getFormData('refundsFilterForm');
        fetch(`{{ route('admin.reports.search.refunds') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('refundsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No refund requests found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(r => `
                        <tr>
                            <td>#${r.id}</td>
                            <td>${r.passenger_name}</td>
                            <td>${r.passenger_phone}</td>
                            <td>${r.route}</td>
                            <td>Nu. ${r.amount}</td>
                            <td>${r.cancelled_at}</td>
                            <td><span class="badge bg-${r.refund_status === 'pending' ? 'warning' : 'success'}">${r.refund_status}</span></td>
                            <td>
                                ${r.refund_status === 'pending' ? `<button class="btn btn-sm btn-success" onclick="processRefund(${r.id}, 'refunded')"><i class="bi bi-check-circle"></i> Refund</button>` : `<span class="text-success"><i class="bi bi-check-circle"></i> Done</span>`}
                                <a href="/admin/bookings/${r.id}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    `).join('');
                }
                document.getElementById('refundsCount').textContent = `${data.length} refund requests`;
            });
    }

    function searchPayments() {
        const params = getFormData('paymentsFilterForm');
        fetch(`{{ route('admin.reports.search.payments') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('paymentsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No payments found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(p => `
                        <tr>
                            <td>${p.id}</td>
                            <td>#${p.booking_id}</td>
                            <td>Nu. ${p.amount}</td>
                            <td>${p.method}</td>
                            <td>${p.txn_id || '-'}</td>
                            <td><span class="badge bg-${p.status === 'completed' ? 'success' : p.status === 'pending' ? 'warning' : 'danger'}">${p.status}</span></td>
                            <td>${p.date}</td>
                            <td><button class="btn btn-sm btn-outline-primary" onclick="editPayment(${p.id}, '${p.status}')" title="Edit"><i class="bi bi-pencil"></i></button></td>
                        </tr>
                    `).join('');
                }
                document.getElementById('paymentsCount').textContent = `${data.length} records found`;
            });
    }

    function searchDrivers() {
        const params = getFormData('driversFilterForm');
        fetch(`{{ route('admin.reports.search.drivers') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('driversTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No drivers found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(d => `
                        <tr>
                            <td>${d.id}</td>
                            <td>${d.name}</td>
                            <td>${d.phone}</td>
                            <td>${d.license}</td>
                            <td>${d.vehicle}</td>
                            <td><span class="badge bg-${d.verified ? 'success' : 'warning'}">${d.verified ? 'Yes' : 'No'}</span></td>
                            <td><span class="badge bg-${d.active ? 'success' : 'secondary'}">${d.active ? 'Yes' : 'No'}</span></td>
                            <td><a href="/admin/drivers/${d.id}" class="btn btn-sm btn-outline-primary" title="View/Edit"><i class="bi bi-pencil"></i></a></td>
                        </tr>
                    `).join('');
                }
                document.getElementById('driversCount').textContent = `${data.length} records found`;
            });
    }

    function searchPayouts() {
        const params = getFormData('payoutsFilterForm');
        fetch(`{{ route('admin.reports.search.payouts') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('payoutsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No payouts found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(p => `
                        <tr>
                            <td>${p.id}</td>
                            <td>${p.driver_name}</td>
                            <td>${p.trip_route}</td>
                            <td>Nu. ${p.total}</td>
                            <td>Nu. ${p.charge}</td>
                            <td>Nu. ${p.payout}</td>
                            <td><span class="badge bg-${p.status === 'completed' ? 'success' : 'warning'}">${p.status}</span></td>
                            <td>${p.status === 'pending' ? `<button class="btn btn-sm btn-success" onclick="markPayoutComplete(${p.id})"><i class="bi bi-check-circle"></i></button>` : '-'}</td>
                        </tr>
                    `).join('');
                }
                document.getElementById('payoutsCount').textContent = `${data.length} records found`;
            });
    }

    function editBooking(id, status, paymentStatus) {
        currentEditType = 'booking';
        currentEditId = id;
        document.getElementById('editModalTitle').textContent = 'Edit Booking #' + id;
        document.getElementById('editModalBody').innerHTML = `
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="editStatus">
                    <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="confirmed" ${status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                    <option value="completed" ${status === 'completed' ? 'selected' : ''}>Completed</option>
                    <option value="cancelled" ${status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Status</label>
                <select class="form-select" id="editPaymentStatus">
                    <option value="pending" ${paymentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="paid" ${paymentStatus === 'paid' ? 'selected' : ''}>Paid</option>
                    <option value="failed" ${paymentStatus === 'failed' ? 'selected' : ''}>Failed</option>
                </select>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function editPayment(id, status) {
        currentEditType = 'payment';
        currentEditId = id;
        document.getElementById('editModalTitle').textContent = 'Edit Payment #' + id;
        document.getElementById('editModalBody').innerHTML = `
            <div class="mb-3">
                <label class="form-label">Payment Status</label>
                <select class="form-select" id="editStatus">
                    <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="completed" ${status === 'completed' ? 'selected' : ''}>Completed</option>
                    <option value="failed" ${status === 'failed' ? 'selected' : ''}>Failed</option>
                </select>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function saveEdit() {
        const btn = document.getElementById('saveEditBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
        let url, data;
        if (currentEditType === 'booking') {
            url = `/admin/reports/update/booking/${currentEditId}`;
            data = { status: document.getElementById('editStatus').value, payment_status: document.getElementById('editPaymentStatus').value };
        } else if (currentEditType === 'payment') {
            url = `/admin/reports/update/payment/${currentEditId}`;
            data = { status: document.getElementById('editStatus').value };
        }
        fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(data) })
        .then(res => res.json())
        .then(result => {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            if (result.success) {
                alert('Updated successfully!');
                if (currentEditType === 'booking') searchBookings();
                if (currentEditType === 'payment') searchPayments();
            } else { alert('Error: ' + (result.message || 'Update failed')); }
        })
        .finally(() => { btn.disabled = false; btn.innerHTML = 'Save Changes'; });
    }

    function processRefund(bookingId, status) {
        if (!confirm('Mark this booking as refunded?')) return;
        fetch(`/admin/reports/update/refund/${bookingId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ refund_status: status }) })
        .then(res => res.json())
        .then(result => {
            if (result.success) { alert('Refund processed!'); searchRefunds(); loadPendingRefundsCount(); }
            else { alert('Error: ' + (result.message || 'Failed')); }
        });
    }

    function markPayoutComplete(payoutId) {
        if (!confirm('Mark payout as completed?')) return;
        fetch(`/admin/reports/update/payout/${payoutId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ status: 'completed' }) })
        .then(res => res.json())
        .then(result => {
            if (result.success) { alert('Payout completed!'); searchPayouts(); }
            else { alert('Error: ' + (result.message || 'Failed')); }
        });
    }

    function exportTrips() { window.location.href = `{{ route('admin.export.trips') }}?${getFormData('tripsFilterForm')}`; }
    function exportBookings() { window.location.href = `{{ route('admin.export.bookings') }}?${getFormData('bookingsFilterForm')}`; }
    function exportRefunds() { window.location.href = `{{ route('admin.export.bookings') }}?refund_filter=1&${getFormData('refundsFilterForm')}`; }
    function exportPayments() { window.location.href = `{{ route('admin.export.payments') }}?${getFormData('paymentsFilterForm')}`; }
    function exportDrivers() { window.location.href = `{{ route('admin.export.drivers') }}?${getFormData('driversFilterForm')}`; }
    function exportPayouts() { window.location.href = `{{ route('admin.export.payouts') }}?${getFormData('payoutsFilterForm')}`; }
</script>
@endpush
