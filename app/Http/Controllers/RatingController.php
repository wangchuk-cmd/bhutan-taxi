<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rating;
use App\Mail\DriverRatingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    /**
     * Show rating form for a booking after payment
     */
    public function show($bookingId)
    {
        $booking = Booking::with('trip.driver', 'passenger')->findOrFail($bookingId);

        // Check if booking belongs to current user
        if ($booking->passenger_id !== Auth::id()) {
            abort(403, 'You cannot rate this booking.');
        }

        // Check if payment is completed
        if ($booking->payment_status !== 'paid') {
            abort(403, 'You can rate this driver after payment is completed.');
        }

        // Check if rating already exists
        $existingRating = $booking->rating;

        return view('ratings.show', [
            'booking' => $booking,
            'rating' => $existingRating,
        ]);
    }

    /**
     * Store the rating
     */
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::with('trip.driver')->findOrFail($bookingId);

        // Validate request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        // Check authorization
        if ($booking->passenger_id !== Auth::id()) {
            abort(403, 'You cannot rate this booking.');
        }

        // Check if payment is completed
        if ($booking->payment_status !== 'paid') {
            abort(403, 'You can rate this driver after payment is completed.');
        }

        // Create or update rating
        $rating = Rating::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'driver_id' => $booking->trip->driver_id,
                'passenger_id' => Auth::id(),
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        // Send notification to driver
        if ($booking->trip->driver->user->email) {
            Mail::to($booking->trip->driver->user->email)
                ->queue(new DriverRatingNotification($rating, $booking));
        }

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Thank you! Your rating has been submitted.');
    }

    /**
     * Get driver's average rating and reviews
     */
    public function getDriverRatings($driverId)
    {
        $ratings = Rating::forDriver($driverId)
            ->with('passenger')
            ->latest()
            ->paginate(10);

        $averageRating = Rating::getDriverAverageRating($driverId);
        $ratingCount = Rating::getDriverRatingCount($driverId);

        return response()->json([
            'average_rating' => round($averageRating, 2),
            'rating_count' => $ratingCount,
            'ratings' => $ratings,
        ]);
    }

    /**
     * Get driver profile with ratings
     */
    public function getDriverProfile($driverId)
    {
        $driver = \App\Models\Driver::with('user', 'ratings.passenger')
            ->findOrFail($driverId);

        $averageRating = $driver->getAverageRating();
        $ratingCount = $driver->getRatingCount();
        $recentRatings = $driver->ratings()
            ->with('passenger')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'driver' => $driver,
            'average_rating' => round($averageRating, 2),
            'rating_count' => $ratingCount,
            'recent_ratings' => $recentRatings,
        ]);
    }

    /**
     * Show driver's ratings and feedback (Driver view)
     */
    public function driverRatings()
    {
        $driver = Auth::user()->driver;
        
        if (!$driver) {
            abort(403, 'You are not a driver.');
        }

        try {
            $ratings = Rating::where('driver_id', $driver->id)
                ->with('passenger', 'booking.trip')
                ->latest()
                ->paginate(15);

            $averageRating = $driver->getAverageRating();
            $ratingCount = $driver->getRatingCount();
            $ratingBreakdown = $this->getRatingBreakdown($driver->id);
        } catch (\Exception $e) {
            // Handle case where ratings table doesn't exist yet
            $ratings = collect([]);
            $averageRating = 0;
            $ratingCount = 0;
            $ratingBreakdown = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        }

        return view('driver.ratings', [
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'ratingCount' => $ratingCount,
            'ratingBreakdown' => $ratingBreakdown,
        ]);
    }

    /**
     * Get rating breakdown for a driver (count per star)
     */
    private function getRatingBreakdown($driverId)
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = Rating::where('driver_id', $driverId)
                ->where('rating', $i)
                ->count();
        }
        return $breakdown;
    }

    /**
     * Show admin view of all ratings (Admin view)
     */
    public function adminRatings()
    {
        try {
            $ratings = Rating::with('driver.user', 'passenger', 'booking.trip')
                ->latest()
                ->paginate(20);

            $drivers = \App\Models\Driver::with('user')
                ->whereHas('ratings')
                ->withCount('ratings')
                ->withAvg('ratings', 'rating')
                ->get();
        } catch (\Exception $e) {
            // Handle case where ratings table doesn't exist yet
            $ratings = collect([]);
            $drivers = collect([]);
        }

        return view('admin.ratings', [
            'ratings' => $ratings,
            'drivers' => $drivers,
        ]);
    }

    /**
     * Show single driver's ratings in admin panel
     */
    public function adminDriverRatings($driverId)
    {
        $driver = \App\Models\Driver::with('user')->findOrFail($driverId);
        
        try {
            $ratings = Rating::where('driver_id', $driverId)
                ->with('passenger', 'booking.trip')
                ->latest()
                ->paginate(15);

            $averageRating = $driver->getAverageRating();
            $ratingCount = $driver->getRatingCount();
            $ratingBreakdown = $this->getRatingBreakdown($driverId);
        } catch (\Exception $e) {
            // Handle case where ratings table doesn't exist yet
            $ratings = collect([]);
            $averageRating = 0;
            $ratingCount = 0;
            $ratingBreakdown = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        }

        return view('admin.driver-ratings', [
            'driver' => $driver,
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'ratingCount' => $ratingCount,
            'ratingBreakdown' => $ratingBreakdown,
        ]);
    }
}
