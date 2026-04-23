<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Payout;
use App\Models\Setting;
use App\Mail\BookingConfirmation;
use App\Mail\DriverBookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function process($bookingId)
    {
        $booking = Booking::with(['trip.route', 'trip.driver.user'])
            ->where('passenger_id', Auth::id())
            ->where('payment_status', 'pending')
            ->findOrFail($bookingId);

        $amount = $booking->total_amount;
        $timeRemaining = Setting::get('payment_timeout_seconds', 300); // 5 minutes for OTP timeout

        return view('payment.process', compact('booking', 'amount', 'timeRemaining'));
    }

    public function complete(Request $request, $bookingId)
    {
        $booking = Booking::with(['trip.driver.user', 'trip.route', 'passenger'])
            ->where('passenger_id', Auth::id())
            ->where('payment_status', 'pending')
            ->findOrFail($bookingId);

        $trip = $booking->trip;

        // Check if seats are still available (first-pay-first-get)
        if (!$trip->hasAvailableSeats($booking->seats_booked)) {
            $booking->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            return redirect()->route('home')->with('error', 'Sorry, the seats were taken by another passenger. Please try another trip.');
        }

        DB::transaction(function () use ($request, $booking, $trip) {
            // Calculate amount based on booking type
            $amount = $booking->booking_type === 'full' 
                ? $trip->full_taxi_price 
                : $trip->price_per_seat * $booking->seats_booked;

            // Extract method and account details for the slip
            $methodString = $request->input('payment_method', 'RMA (Mock)');
            if ($request->filled('account_last4')) {
                $methodString .= ' (Acct: ...' . $request->input('account_last4') . ')';
            }

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'status' => 'completed',
                'payment_method' => $methodString,
                'transaction_time' => now(),
            ]);

            // Update booking
            $booking->update([
                'payment_status' => 'paid',
                'payment_time' => now(),
            ]);

            // Reduce available seats
            $trip->decrement('available_seats', $booking->seats_booked);

            // Create payout record
            $serviceCharge = Payout::calculateServiceCharge($amount);
            $payoutStatus = Setting::get('driver_payout_time', '24') === 'immediate' ? 'completed' : 'pending';
            $paidAt = $payoutStatus === 'completed' ? now() : null;
            Payout::create([
                'driver_id' => $trip->driver_id,
                'trip_id' => $trip->id,
                'total_amount' => $amount,
                'service_charge' => $serviceCharge,
                'payout_amount' => $amount - $serviceCharge,
                'status' => $payoutStatus,
                'paid_at' => $paidAt,
            ]);

            // Notify passenger
            Notification::send(
                $booking->passenger_id,
                'payment',
                'Payment successful! Your booking for ' . $trip->origin_dzongkhag . ' → ' . $trip->destination_dzongkhag . ' is confirmed.'
            );

            // Notify driver
            Notification::send(
                $trip->driver->user_id,
                'booking',
                'New booking received! ' . $booking->seats_booked . ' seat(s) booked for your trip on ' . $trip->departure_datetime->format('M d, Y H:i')
            );

            // Send email confirmation to passenger
            if ($booking->passenger && $booking->passenger->email) {
                try {
                    Mail::to($booking->passenger->email)->send(new BookingConfirmation($booking));
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
                }
            }

            // Send email notification to driver
            if ($trip->driver && $trip->driver->user && $trip->driver->user->email) {
                try {
                    Mail::to($trip->driver->user->email)->send(new DriverBookingNotification($booking));
                } catch (\Exception $e) {
                    \Log::error('Failed to send driver notification email: ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('booking.receipt', $booking->id)->with('success', 'Payment successful! Your booking is confirmed.');
    }

    public function timeout($bookingId)
    {
        $booking = Booking::where('passenger_id', Auth::id())
            ->where('payment_status', 'pending')
            ->find($bookingId);

        if ($booking) {
            $booking->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);
        }

        return redirect()->route('home')->with('error', 'Payment timeout. Please try booking again.');
    }

    public function cancel($bookingId)
    {
        $booking = Booking::where('passenger_id', Auth::id())
            ->where('payment_status', 'pending')
            ->find($bookingId);

        if ($booking) {
            $booking->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);
        }

        return redirect()->route('home')->with('info', 'Payment cancelled. Your transaction has been cancelled.');
    }
}
