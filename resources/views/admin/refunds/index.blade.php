@extends('layouts.admin')

@section('title', 'Manage Refunds')

@section('content')
@include('components.confirm-modal')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-arrow-counterclockwise me-2"></i>Refund Management</h2>
            <p class="text-muted mb-0">Review, approve, reject, and export refund requests.</p>
        </div>
        <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary">
            <i class="bi bi-graph-up me-2"></i>Open Reports
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Open Queue</div>
                    <div class="fs-3 fw-bold">{{ $stats['open'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Refunded</div>
                    <div class="fs-3 fw-bold">{{ $stats['refunded'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Rejected</div>
                    <div class="fs-3 fw-bold">{{ $stats['rejected'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><i class="bi bi-table me-2"></i>Refund Requests</div>
            <span class="badge bg-light text-dark" id="refundsCountBadge">Loading...</span>
        </div>
        <div class="card-body">
            <form id="refundsFilterForm" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small" title="e.g. 7,9,10">Days</label>
                        <input type="text" name="specific_dates" class="form-control form-control-sm" placeholder="7,9,10" title="Enter specific days separated by commas">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Status</label>
                        <select name="refund_status" class="form-select form-select-sm">
                            <option value="open" selected>Open Queue</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="refunded">Refunded</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name/Phone/Booking ID">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-sm w-100" onclick="searchRefunds()">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-sm table-striped table-hover align-middle">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Booking ID</th>
                            <th>Passenger</th>
                            <th>Phone</th>
                            <th>Route</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Transaction</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="refundsTableBody">
                        <tr><td colspan="10" class="text-center text-muted">Click Search to load refund requests</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span id="refundsCount" class="text-muted small"></span>
                <button type="button" class="btn btn-success" onclick="exportRefunds()">
                    <i class="bi bi-download me-2"></i>Export Refunds CSV
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadRefunds();
        loadOpenRefundCount();
    });

    function getFormData(formId) {
        const form = document.getElementById(formId);
        const data = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of data.entries()) {
            if (value !== null && value !== undefined && value !== '') params.append(key, value);
        }
        return params.toString();
    }

    function loadOpenRefundCount() {
        fetch(`{{ route('admin.reports.search.refunds') }}?refund_status=open`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('refundsCountBadge').textContent = `${data.length} open`;
            });
    }

    function loadRefunds() {
        searchRefunds();
    }

    function searchRefunds() {
        const params = getFormData('refundsFilterForm');
        fetch(`{{ route('admin.reports.search.refunds') }}?${params}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('refundsTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No refund requests found</td></tr>';
                } else {
                    tbody.innerHTML = data.map(r => `
                        <tr id="refund-row-${r.id}">
                            <td>#${r.booking_id}</td>
                            <td>${r.passenger_name}</td>
                            <td>${r.passenger_phone}</td>
                            <td>${r.route}</td>
                            <td>Nu. ${r.amount}</td>
                            <td>${r.reason || '-'}</td>
                            <td class="text-break">${r.transaction_id || '-'}</td>
                            <td><span class="badge bg-${r.refund_status === 'pending' || r.refund_status === 'under_review' ? 'warning' : r.refund_status === 'rejected' ? 'danger' : 'success'}">${r.refund_status}</span></td>
                            <td>${r.verified ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Pending</span>'}</td>
                            <td>
                                ${(r.refund_status === 'pending' || r.refund_status === 'under_review') ? `<button class="btn btn-sm btn-success me-1" onclick="processRefund(${r.id}, 'refunded')"><i class="bi bi-check-circle"></i> Verify & Refund</button> <button class="btn btn-sm btn-outline-danger" onclick="processRefund(${r.id}, 'rejected')"><i class="bi bi-x-circle"></i> Reject</button>` : `<span class="text-success"><i class="bi bi-check-circle"></i> Done</span>`}
                                <a href="/admin/bookings/${r.booking_id}" class="btn btn-sm btn-outline-info ms-1" title="View"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    `).join('');
                }
                document.getElementById('refundsCount').textContent = `${data.length} refund requests`;
                document.getElementById('refundsCountBadge').textContent = `${data.length} loaded`;

                // If page was opened with ?refundId=, scroll to and highlight that row
                try {
                    const params = new URLSearchParams(window.location.search);
                    const refundIdParam = params.get('refundId');
                    if (refundIdParam) {
                        const target = document.getElementById('refund-row-' + refundIdParam);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            target.classList.add('highlight-refund');
                            setTimeout(() => target.classList.remove('highlight-refund'), 4000);
                        }
                    }
                } catch (e) { console.error('highlight refund error', e); }
            });
    }

    function processRefund(id, status) {
        const message = status === 'refunded' ? 'Verify the transaction and process this refund?' : 'Reject this refund request?';
        const title = status === 'refunded' ? 'Verify & Refund' : 'Reject Refund';

        showConfirmModal(message, title, function(adminNotes) {
            fetch(`{{ route('admin.reports.update.refund', ['id' => '__ID__']) }}`.replace('__ID__', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ refund_status: status, admin_notes: adminNotes || '' })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    // Use a nicer toast if available, fallback to alert
                    try { toast('success', result.message || 'Updated successfully!'); } catch(e) { alert(result.message || 'Updated successfully!'); }
                    searchRefunds();
                    loadOpenRefundCount();
                } else {
                    try { toast('error', result.message || 'Failed'); } catch(e) { alert('Error: ' + (result.message || 'Failed')); }
                }
            });
        }, { showInput: true, inputPlaceholder: status === 'refunded' ? 'Admin notes for approval (optional)' : 'Admin notes for rejection (optional)' });
    }

    function exportRefunds() {
        window.location.href = `{{ route('admin.export.refunds') }}?${getFormData('refundsFilterForm')}`;
    }
</script>
@endpush

@push('styles')
<style>
    .highlight-refund {
        animation: refundHighlight 1s ease-in-out 0s 3;
        box-shadow: 0 8px 24px rgba(59,130,246,0.08);
        border-left: 4px solid rgba(59,130,246,0.8);
    }

    @keyframes refundHighlight {
        0% { background-color: rgba(59,130,246,0.12); }
        50% { background-color: rgba(59,130,246,0.06); }
        100% { background-color: transparent; }
    }
</style>
@endpush
