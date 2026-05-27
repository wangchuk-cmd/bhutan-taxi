<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RefundRequest;
use App\Models\Notification;
use App\Models\Setting;
use App\Mail\RefundRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create($tripId)
    {
        $trip = Trip::with(['driver:id,user_id,vehicle_type,fuel_type,taxi_plate_number,years_of_experience,show_experience_to_public,show_age_range_to_public,public_age_range', 'driver.user:id,name,profile_image', 'route'])->findOrFail($tripId);
        
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
            'passengers.*.phone' => ['required', 'string', 'size:' . Setting::getPhoneNumberDigits(), 'regex:' . Setting::getPhoneNumberRegex()],
            'booking_type' => 'required|in:shared,full',
            'seats_booked' => 'required|integer|min:1',
        ], [
            'passengers.*.phone.size' => Setting::getPhoneNumberHint(),
            'passengers.*.phone.regex' => Setting::getPhoneNumberHint(),
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        // Check seat availability
        $seatsNeeded = $validated['booking_type'] === 'full' ? $trip->total_seats : $validated['seats_booked'];

        if ($validated['booking_type'] === 'shared' && $validated['seats_booked'] > $trip->available_seats) {
            return back()
                ->withInput()
                ->with('error', 'Only ' . $trip->available_seats . ' seat' . ($trip->available_seats === 1 ? '' : 's') . ' are available for this trip.');
        }
        
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
        
        $bookings = Booking::with(['trip.route', 'trip.driver:id,user_id,vehicle_type,fuel_type,taxi_plate_number,years_of_experience,show_experience_to_public,show_age_range_to_public,public_age_range', 'trip.driver.user:id,name,profile_image', 'payment', 'latestRefundRequest'])
            ->where('passenger_id', Auth::id())
            ->where('payment_status', 'paid')
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
        $booking = Booking::with(['trip.route', 'trip.driver:id,user_id,vehicle_type,fuel_type,taxi_plate_number,years_of_experience,show_experience_to_public,show_age_range_to_public,public_age_range,average_rating,rating_count', 'trip.driver.user:id,name,phone_number,profile_image', 'payment', 'latestRefundRequest'])
            ->where('passenger_id', Auth::id())
            ->findOrFail($id);

        // Get driver's ratings for display
        $driverId = $booking->trip->driver->id;
        $driverRatings = \App\Models\Rating::where('driver_id', $driverId)
            ->with('passenger:id,name')
            ->latest()
            ->limit(3)
            ->get();

        return view('booking.show', compact('booking', 'driverRatings'));
    }

    public function receipt($id)
    {
        $booking = Booking::with(['trip.route', 'trip.driver:id,user_id,vehicle_type,fuel_type,taxi_plate_number,years_of_experience,show_experience_to_public,show_age_range_to_public,public_age_range', 'trip.driver.user:id,name,profile_image', 'payment'])
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
                'Your booking for ' . $booking->trip->origin_dzongkhag . ' → ' . $booking->trip->destination_dzongkhag . ' has been cancelled. Refund processed.',
                null,
                ['url' => route('bookings.show', $booking->id)]
            );

            // Notify driver
            Notification::send(
                $booking->trip->driver->user_id,
                'cancellation',
                'A passenger cancelled their booking for your trip on ' . $booking->trip->departure_datetime->format('M d, Y H:i'),
                null,
                ['url' => route('driver.trips')]
            );
        });

        return redirect()->route('bookings.my')->with('success', 'Booking cancelled successfully. Full refund has been processed.');
    }

    public function requestRefund(Request $request, $id)
    {
        $booking = Booking::with(['trip', 'payment', 'latestRefundRequest'])
            ->where('passenger_id', Auth::id())
            ->findOrFail($id);

        if ($booking->payment_status !== 'paid' || !$booking->payment) {
            return back()->with('error', 'Refund requests are only available for paid bookings.');
        }

        if ($booking->latestRefundRequest && in_array($booking->latestRefundRequest->status, ['pending', 'under_review', 'refunded'])) {
            return back()->with('error', 'A refund request already exists for this booking.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $requiresTransactionId = Setting::get('refund_requires_transaction_id', true);
        if ($requiresTransactionId && empty($validated['transaction_id'])) {
            return back()->withInput()->with('error', 'Please enter the transaction ID so admin can verify the payment.');
        }

        $deadlineHours = (int) Setting::get('refund_request_deadline_hours', 24);
        $status = now()->diffInHours($booking->trip->departure_datetime, false) <= $deadlineHours ? 'under_review' : 'pending';
        $amount = $booking->payment->amount ?? $booking->total_amount;

        $refundRequest = RefundRequest::create([
            'booking_id' => $booking->id,
            'passenger_id' => Auth::id(),
            'payment_id' => $booking->payment->id,
            'transaction_id' => $validated['transaction_id'] ?: $booking->payment->transaction_id,
            'amount' => $amount,
            'reason' => $validated['reason'],
            'status' => $status,
        ]);

        $booking->update([
            'refund_status' => 'pending',
        ]);

        Notification::send(
            Auth::id(),
            'refund_review',
            'Your refund request for booking #' . $booking->id . ' has been submitted and is now ' . str_replace('_', ' ', $status) . '.',
            null,
            ['url' => route('bookings.show', $booking->id)]
        );

        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');
        foreach ($adminIds as $adminId) {
            Notification::send(
                $adminId,
                'refund_request',
                'New refund request #' . $refundRequest->id . ' for booking #' . $booking->id . ' needs review.',
                null,
                ['url' => route('admin.refunds') . '?refundId=' . $refundRequest->id]
            );
        }

        $adminsWithEmail = \App\Models\User::where('role', 'admin')
            ->whereNotNull('email')
            ->get();

        foreach ($adminsWithEmail as $admin) {
            try {
                Mail::to($admin->email)->send(new RefundRequestNotification($refundRequest));
            } catch (\Throwable $e) {
                \Log::error('Failed to send refund request email to admin ' . $admin->id . ': ' . $e->getMessage());
            }
        }

        if ($booking->passenger && $booking->passenger->email) {
            try {
                Mail::to($booking->passenger->email)->send(new RefundRequestNotification($refundRequest, true));
            } catch (\Throwable $e) {
                \Log::error('Failed to send refund request confirmation email to passenger ' . $booking->passenger_id . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Refund request submitted successfully.');
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
