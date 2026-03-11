@extends('layouts.admin')

@section('title', 'Complaints & Feedback')

@section('content')
<h4 class="mb-4"><i class="bi bi-chat-square-text me-2"></i>Complaints & Feedback</h4>

<div class="card">
    <div class="card-body">
        @if($complaints->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Related Trip</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr class="{{ $complaint->status === 'pending' ? 'table-warning' : '' }}">
                                <td>
                                    <strong>{{ $complaint->user->name }}</strong><br>
                                    <small class="badge bg-{{ $complaint->user->role === 'driver' ? 'secondary' : 'light text-dark' }}">{{ ucfirst($complaint->user->role) }}</small>
                                </td>
                                <td><span class="badge bg-{{ $complaint->type === 'complaint' ? 'danger' : 'info' }}">{{ ucfirst($complaint->type) }}</span></td>
                                <td>{{ Str::limit($complaint->subject, 30) }}</td>
                                <td>
                                    @if($complaint->trip)
                                        {{ $complaint->trip->origin_dzongkhag }} → {{ $complaint->trip->destination_dzongkhag }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($complaint->status === 'resolved')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Resolved</span>
                                    @else
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#complaint{{ $complaint->id }}">
                                            <i class="bi bi-reply me-1"></i>Respond
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="complaint{{ $complaint->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $complaint->subject }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted mb-2">
                                                <strong>From:</strong> {{ $complaint->user->name }} 
                                                <span class="badge bg-{{ $complaint->user->role === 'driver' ? 'secondary' : 'info' }}">{{ ucfirst($complaint->user->role) }}</span><br>
                                                <strong>Email:</strong> {{ $complaint->user->email }}<br>
                                                <strong>Type:</strong> {{ ucfirst($complaint->type) }}<br>
                                                <strong>Date:</strong> {{ $complaint->created_at->format('M d, Y h:i A') }}
                                            </p>
                                            <hr>
                                            <p>{{ $complaint->message }}</p>

                                            @if($complaint->admin_response)
                                                <hr>
                                                <div class="bg-light p-3 rounded">
                                                    <strong>Admin Response:</strong>
                                                    <p class="mb-0">{{ $complaint->admin_response }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            @if($complaint->status !== 'resolved')
                                                <form action="{{ route('admin.complaints.respond', $complaint->id) }}" method="POST" class="w-100">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <textarea name="admin_response" class="form-control" placeholder="Write your response..." rows="2" required></textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-check-circle me-1"></i>Send Response & Resolve
                                                        </button>
                                                    </div>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $complaints->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text display-1 text-muted"></i>
                <p class="mt-3 text-muted">No complaints or feedback yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
