<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Complaint;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Auto-mark displayed notifications as read
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->whereIn('id', $notifications->pluck('id'))
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    // Complaints/Feedback
    public function createComplaint()
    {
        // Get user's trips from their bookings
        $trips = Trip::whereIn('id', function($query) {
            $query->select('trip_id')
                ->from('bookings')
                ->where('passenger_id', Auth::id());
        })->orderBy('departure_datetime', 'desc')->get();

        return view('complaints.create', compact('trips'));
    }

    public function storeComplaint(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:complaint,feedback',
            'trip_id' => 'nullable|exists:trips,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'trip_id' => $validated['trip_id'] ?? null,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', 'Your ' . $validated['type'] . ' has been submitted. We will review it shortly.');
    }
}
