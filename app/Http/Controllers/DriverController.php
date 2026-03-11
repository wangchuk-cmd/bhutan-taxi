<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\Notification;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function dashboard()
    {
        $driver = Auth::user()->driver;
        
        $upcomingTrips = Trip::with(['route', 'bookings.passenger'])
            ->where('driver_id', $driver->id)
            ->active()
            ->upcoming()
            ->orderBy('departure_datetime')
            ->take(5)
            ->get();

        $totalEarnings = Payout::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->sum('payout_amount');

        $pendingPayouts = Payout::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->sum('payout_amount');

        $totalTrips = Trip::where('driver_id', $driver->id)->count();
        $completedTrips = Trip::where('driver_id', $driver->id)->where('status', 'completed')->count();

        return view('driver.dashboard', compact(
            'driver', 'upcomingTrips', 'totalEarnings', 'pendingPayouts', 'totalTrips', 'completedTrips'
        ));
    }

    public function trips()
    {
        $driver = Auth::user()->driver;
        
        $trips = Trip::with(['route', 'bookings'])
            ->where('driver_id', $driver->id)
            ->orderBy('departure_datetime', 'desc')
            ->paginate(10);

        return view('driver.trips', compact('trips'));
    }

    public function createTrip()
    {
        $driver = Auth::user()->driver;

        if (!$driver->verified) {
            return redirect()->route('driver.dashboard')
                ->with('error', 'Your account is not verified yet. Please wait for admin approval.');
        }

        $locations = config('dzongkhags.list');
        
        return view('driver.create-trip', compact('locations'));
    }

    public function storeTrip(Request $request)
    {
        $driver = Auth::user()->driver;

        if (!$driver->verified) {
            return back()->with('error', 'Your account is not verified yet.');
        }

        $locations = config('dzongkhags.list');
        
        $validated = $request->validate([
            'origin_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations)],
            'destination_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations), 'different:origin_dzongkhag'],
            'departure_datetime' => 'required|date|after:now',
            'total_seats' => 'required|integer|min:1|max:12',
            'price_per_seat' => 'required|numeric|min:0',
            'full_taxi_price' => 'required|numeric|min:0',
        ]);

        // Find or create matching route
        $route = Route::where('origin_dzongkhag', $validated['origin_dzongkhag'])
            ->where('destination_dzongkhag', $validated['destination_dzongkhag'])
            ->first();

        Trip::create([
            'driver_id' => $driver->id,
            'route_id' => $route?->id,
            'origin_dzongkhag' => $validated['origin_dzongkhag'],
            'destination_dzongkhag' => $validated['destination_dzongkhag'],
            'departure_datetime' => $validated['departure_datetime'],
            'total_seats' => $validated['total_seats'],
            'available_seats' => $validated['total_seats'],
            'price_per_seat' => $validated['price_per_seat'],
            'full_taxi_price' => $validated['full_taxi_price'],
            'status' => 'active',
        ]);

        return redirect()->route('driver.trips')->with('success', 'Trip created successfully!');
    }

    public function editTrip($id)
    {
        $driver = Auth::user()->driver;
        $trip = Trip::where('driver_id', $driver->id)->findOrFail($id);
        $locations = config('dzongkhags.list');

        return view('driver.edit-trip', compact('trip', 'locations'));
    }

    public function updateTrip(Request $request, $id)
    {
        $driver = Auth::user()->driver;
        $trip = Trip::where('driver_id', $driver->id)->findOrFail($id);
        $locations = config('dzongkhags.list');

        $validated = $request->validate([
            'origin_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations)],
            'destination_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations), 'different:origin_dzongkhag'],
            'departure_datetime' => 'required|date|after:now',
            'total_seats' => 'required|integer|min:' . ($trip->total_seats - $trip->available_seats),
            'price_per_seat' => 'required|numeric|min:0',
            'full_taxi_price' => 'required|numeric|min:0',
        ]);

        // Find matching route
        $route = Route::where('origin_dzongkhag', $validated['origin_dzongkhag'])
            ->where('destination_dzongkhag', $validated['destination_dzongkhag'])
            ->first();

        $bookedSeats = $trip->total_seats - $trip->available_seats;
        $newAvailableSeats = $validated['total_seats'] - $bookedSeats;

        $trip->update([
            'route_id' => $route?->id,
            'origin_dzongkhag' => $validated['origin_dzongkhag'],
            'destination_dzongkhag' => $validated['destination_dzongkhag'],
            'departure_datetime' => $validated['departure_datetime'],
            'total_seats' => $validated['total_seats'],
            'available_seats' => $newAvailableSeats,
            'price_per_seat' => $validated['price_per_seat'],
            'full_taxi_price' => $validated['full_taxi_price'],
        ]);

        return redirect()->route('driver.trips')->with('success', 'Trip updated successfully!');
    }

    public function cancelTrip($id)
    {
        $driver = Auth::user()->driver;
        $trip = Trip::with('bookings.passenger')->where('driver_id', $driver->id)->findOrFail($id);

        // Cancel all active bookings and notify passengers
        foreach ($trip->bookings()->active()->get() as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_time' => now(),
                'refund_status' => $booking->payment_status === 'paid' ? 'refunded' : 'none',
            ]);

            if ($booking->payment) {
                $booking->payment->update(['status' => 'refunded']);
            }

            Notification::send(
                $booking->passenger_id,
                'cancellation',
                'Your booked trip ' . $trip->origin_dzongkhag . ' → ' . $trip->destination_dzongkhag . ' on ' . $trip->departure_datetime->format('M d, Y') . ' has been cancelled by the driver. Full refund processed.'
            );
        }

        $trip->update(['status' => 'cancelled']);

        return redirect()->route('driver.trips')->with('success', 'Trip cancelled. All passengers have been notified and refunded.');
    }

    public function passengers($tripId)
    {
        $driver = Auth::user()->driver;
        $trip = Trip::with(['route', 'bookings.passenger'])
            ->where('driver_id', $driver->id)
            ->findOrFail($tripId);

        $bookings = $trip->bookings()->active()->paid()->get();

        return view('driver.passengers', compact('trip', 'bookings'));
    }

    public function payouts()
    {
        $driver = Auth::user()->driver;
        
        $payouts = Payout::with('trip.route')
            ->where('driver_id', $driver->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalPaid = Payout::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->sum('payout_amount');

        $pendingAmount = Payout::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->sum('payout_amount');

        return view('driver.payouts', compact('payouts', 'totalPaid', 'pendingAmount'));
    }

    public function profile()
    {
        $driver = Auth::user()->driver;
        return view('driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $driver = $user->driver;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/|unique:users,phone_number,' . $user->id,
            'vehicle_type' => 'required|string|max:50',
        ], [
            'phone_number.regex' => 'Phone number must contain only digits.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        $driver->update([
            'vehicle_type' => $validated['vehicle_type'],
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Feedback/Complaints
    public function createFeedback()
    {
        $driver = Auth::user()->driver;
        $trips = Trip::where('driver_id', $driver->id)
            ->orderBy('departure_datetime', 'desc')
            ->take(20)
            ->get();

        return view('driver.feedback', compact('trips'));
    }

    public function storeFeedback(Request $request)
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

        return redirect()->route('driver.dashboard')->with('success', 'Your ' . $validated['type'] . ' has been submitted. We will review it shortly.');
    }
}
