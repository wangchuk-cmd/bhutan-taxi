<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create($tripId)
    {
        $trip = Trip::with(['driver.user', 'route'])->findOrFail($tripId);
        
        if (!$trip->hasAvailableSeats()) {
            return back()->with('error', 'No seats available for this trip.');
        }

        return view('booking.create', compact('trip'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.phone' => 'required|string|max:20|regex:/^[0-9]+$/',
            'booking_type' => 'required|in:shared,full',
            'seats_booked' => 'required|integer|min:1',
        ], [
            'passengers.*.phone.regex' => 'Phone number must contain only digits.',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        // Check seat availability
        $seatsNeeded = $validated['booking_type'] === 'full' ? $trip->total_seats : $validated['seats_booked'];
        
        if (!$trip->hasAvailableSeats($seatsNeeded)) {
            return back()->with('error', 'Not enough seats available.');
        }

        // Calculate amount
        $amount = $validated['booking_type'] === 'full' 
            ? $trip->full_taxi_price 
            : $trip->price_per_seat * $validated['seats_booked'];

        // Create booking with pending status
        $booking = Booking::create([
            'trip_id' => $validated['trip_id'],
            'passenger_id' => Auth::id(),
            'passengers_info' => $validated['passengers'],
            'booking_type' => $validated['booking_type'],
            'seats_booked' => $seatsNeeded,
            'payment_status' => 'pending',
            'booking_time' => now(),
            'status' => 'active',
        ]);

        // Redirect to payment page with 10-second timer
        return redirect()->route('payment.process', $booking->id);
    }

    public function myBookings()
    {
        // Get bookings but exclude those completed more than 12 hours ago
        $twelveHoursAgo = now()->subHours(12);
        
        $bookings = Booking::with(['trip.route', 'trip.driver.user', 'payment'])
            ->where('passenger_id', Auth::id())
            ->where(function ($query) use ($twelveHoursAgo) {
                // Show bookings that are either:
                // 1. Not completed yet (departure_datetime is in future)
                // 2. Completed but within 12 hours
                $query->whereHas('trip', function ($subQuery) use ($twelveHoursAgo) {
                    $subQuery->where('departure_datetime', '>', $twelveHoursAgo);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.my-bookings', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['trip.route', 'trip.driver.user', 'payment'])
            ->where('passenger_id', Auth::id())
            ->findOrFail($id);

        return view('booking.show', compact('booking'));
    }

    public function receipt($id)
    {
        $booking = Booking::with(['trip.route', 'trip.driver.user', 'payment'])
            ->where('passenger_id', Auth::id())
            ->where('payment_status', 'paid')
            ->findOrFail($id);

        $payment = $booking->payment;

        if (!$payment) {
            return redirect()->route('bookings.show', $id)->with('error', 'No payment found for this booking.');
        }

        return view('booking.receipt', compact('booking', 'payment'));
    }

    public function cancel($id)
    {
        $booking = Booking::with('trip')
            ->where('passenger_id', Auth::id())
            ->findOrFail($id);

        if (!$booking->canCancel()) {
            return back()->with('error', 'Cannot cancel this booking. Cancellation is only allowed 24+ hours before departure.');
        }

        DB::transaction(function () use ($booking) {
            // Restore seats
            $booking->trip->increment('available_seats', $booking->seats_booked);

            // Update booking
            $booking->update([
                'status' => 'cancelled',
                'cancellation_time' => now(),
                'refund_status' => 'pending',
            ]);

            // Update payment if exists
            if ($booking->payment) {
                $booking->payment->update(['status' => 'refunded']);
                $booking->update(['refund_status' => 'refunded']);
            }

            // Notify user
            Notification::send(
                $booking->passenger_id,
                'cancellation',
                'Your booking for ' . $booking->trip->origin_dzongkhag . ' → ' . $booking->trip->destination_dzongkhag . ' has been cancelled. Refund processed.'
            );

            // Notify driver
            Notification::send(
                $booking->trip->driver->user_id,
                'cancellation',
                'A passenger cancelled their booking for your trip on ' . $booking->trip->departure_datetime->format('M d, Y H:i')
            );
        });

        return redirect()->route('bookings.my')->with('success', 'Booking cancelled successfully. Full refund has been processed.');
    }

    private function getPickupPoints($dzongkhag)
    {
        $points = [
            'Thimphu' => ['Clock Tower', 'Centenary Park', 'Changlimithang', 'Olakha', 'Babesa'],
            'Paro' => ['Paro Town', 'Airport Junction', 'Bondey', 'Drugyal Dzong'],
            'Phuentsholing' => ['Phuentsholing Gate', 'Rinchending', 'Town Center'],
            'Punakha' => ['Punakha Dzong', 'Khuruthang', 'Lobesa'],
            'Wangdue Phodrang' => ['Bajo', 'Wangdue Town', 'Gaselo'],
            'Bumthang' => ['Jakar Town', 'Chamkhar', 'Kurjey'],
            'Trongsa' => ['Trongsa Town', 'Trongsa Dzong'],
            'Mongar' => ['Mongar Town', 'Kilikhar'],
            'Trashigang' => ['Trashigang Town', 'Kanglung'],
            'Samdrup Jongkhar' => ['SJ Gate', 'Dewathang'],
            'Haa' => ['Haa Town', 'Damchu'],
            'Samtse' => ['Samtse Town', 'Tashichhoeling'],
            'Chhukha' => ['Gedu', 'Chhukha Town'],
            'Sarpang' => ['Gelephu', 'Sarpang Town'],
            'Tsirang' => ['Damphu', 'Tsholingkhar'],
            'Dagana' => ['Dagana Town', 'Gesarling'],
            'Gasa' => ['Gasa Town', 'Punakha Junction'],
            'Lhuentse' => ['Lhuentse Town', 'Tangmachu'],
            'Pemagatshel' => ['Pemagatshel Town', 'Nganglam'],
            'Trashiyangtse' => ['Trashiyangtse Town', 'Bumdeling'],
            'Zhemgang' => ['Zhemgang Town', 'Tingtibi'],
        ];

        return $points[$dzongkhag] ?? ['Town Center', 'Bus Station'];
    }
}
