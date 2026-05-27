<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\Notification;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $routes = Route::select(['id', 'origin_dzongkhag', 'destination_dzongkhag', 'distance_km', 'estimated_time'])
            ->orderBy('origin_dzongkhag')
            ->orderBy('destination_dzongkhag')
            ->get();
        $routeData = $this->buildBidirectionalRouteData($routes);
        
        return view('driver.create-trip', compact('locations', 'routes', 'routeData'));
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

        $departureDatetime = Carbon::parse($validated['departure_datetime'])->format('Y-m-d H:i:s');

        $existingTrip = $this->findBlockingActiveTrip(
            $driver->id,
            $departureDatetime,
            $validated['origin_dzongkhag'],
            $validated['destination_dzongkhag']
        );

        if ($existingTrip) {
            return back()
                ->withErrors([
                    'departure_datetime' => 'You already have an active trip that is still in progress. Please complete or cancel the current trip before creating another one.',
                ])
                ->withInput();
        }

        // Find or create matching route
        $route = Route::findBetweenDzongkhags($validated['origin_dzongkhag'], $validated['destination_dzongkhag']);

        if (!$route) {
            return back()->withErrors([
                'destination_dzongkhag' => 'This route is not set by admin yet. Please choose an available admin route so km and estimated time can be calculated.',
            ])->withInput();
        }

        Trip::create([
            'driver_id' => $driver->id,
            'route_id' => $route?->id,
            'origin_dzongkhag' => $validated['origin_dzongkhag'],
            'destination_dzongkhag' => $validated['destination_dzongkhag'],
            'departure_datetime' => $departureDatetime,
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
        $trip = Trip::with('route')->where('driver_id', $driver->id)->findOrFail($id);
        $locations = config('dzongkhags.list');
        $routes = Route::select(['id', 'origin_dzongkhag', 'destination_dzongkhag', 'distance_km', 'estimated_time'])
            ->orderBy('origin_dzongkhag')
            ->orderBy('destination_dzongkhag')
            ->get();
        $routeData = $this->buildBidirectionalRouteData($routes);

        return view('driver.edit-trip', compact('trip', 'locations', 'routes', 'routeData'));
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

        $departureDatetime = Carbon::parse($validated['departure_datetime'])->format('Y-m-d H:i:s');

        $existingTrip = $this->findBlockingActiveTrip(
            $driver->id,
            $departureDatetime,
            $validated['origin_dzongkhag'],
            $validated['destination_dzongkhag'],
            $trip->id
        );

        if ($existingTrip) {
            return back()
                ->withErrors([
                    'departure_datetime' => 'You already have another active trip that is still in progress. Please choose a later schedule or complete the existing trip first.',
                ])
                ->withInput();
        }

        // Find matching route
        $route = Route::findBetweenDzongkhags($validated['origin_dzongkhag'], $validated['destination_dzongkhag']);

        if (!$route) {
            return back()->withErrors([
                'destination_dzongkhag' => 'This route is not set by admin yet. Please choose an available admin route so km and estimated time can be calculated.',
            ])->withInput();
        }

        $bookedSeats = $trip->total_seats - $trip->available_seats;
        $newAvailableSeats = $validated['total_seats'] - $bookedSeats;

        $trip->update([
            'route_id' => $route?->id,
            'origin_dzongkhag' => $validated['origin_dzongkhag'],
            'destination_dzongkhag' => $validated['destination_dzongkhag'],
            'departure_datetime' => $departureDatetime,
            'total_seats' => $validated['total_seats'],
            'available_seats' => $newAvailableSeats,
            'price_per_seat' => $validated['price_per_seat'],
            'full_taxi_price' => $validated['full_taxi_price'],
        ]);

        return redirect()->route('driver.trips')->with('success', 'Trip updated successfully!');
    }

    private function buildBidirectionalRouteData($routes): array
    {
        return $routes
            ->flatMap(function ($route) {
                return [
                    [
                        'origin' => $route->origin_dzongkhag,
                        'destination' => $route->destination_dzongkhag,
                        'distance_km' => $route->distance_km,
                        'estimated_time' => $route->estimated_time,
                    ],
                    [
                        'origin' => $route->destination_dzongkhag,
                        'destination' => $route->origin_dzongkhag,
                        'distance_km' => $route->distance_km,
                        'estimated_time' => $route->estimated_time,
                    ],
                ];
            })
            ->unique(fn ($item) => strtolower(trim($item['origin'])) . '|' . strtolower(trim($item['destination'])))
            ->values()
            ->all();
    }

    private function findBlockingActiveTrip(int $driverId, string $newDepartureDatetime, string $origin, string $destination, ?int $ignoreTripId = null): ?Trip
    {
        $candidateTrips = Trip::with('route')
            ->where('driver_id', $driverId)
            ->where('status', 'active')
            ->when($ignoreTripId, fn ($query) => $query->where('id', '!=', $ignoreTripId))
            ->orderBy('departure_datetime')
            ->get();

        $newDeparture = Carbon::parse($newDepartureDatetime);

        foreach ($candidateTrips as $candidateTrip) {
            $candidateEnd = $candidateTrip->estimated_arrival_at;
            if (!$candidateEnd) {
                continue;
            }

            if ($newDeparture->lessThanOrEqualTo($candidateEnd)) {
                return $candidateTrip;
            }
        }

        return null;
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
                'Your booked trip ' . $trip->origin_dzongkhag . ' → ' . $trip->destination_dzongkhag . ' on ' . $trip->departure_datetime->format('M d, Y') . ' has been cancelled by the driver. Full refund processed.',
                null,
                ['url' => route('bookings.show', $booking->id)]
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

    public function reports(Request $request)
    {
        $driver = Auth::user()->driver;

        $period = $request->get('period', 'monthly');
        if (!in_array($period, ['daily', 'weekly', 'monthly', 'yearly'])) {
            $period = 'monthly';
        }

        [$start, $end, $labels, $keys, $bucketFormat] = $this->buildReportBuckets($period);

        $payouts = Payout::with('trip')
            ->where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereRaw('COALESCE(paid_at, created_at) BETWEEN ? AND ?', [$start, $end])
            ->get();

        $seriesMap = array_fill_keys($keys, 0.0);

        foreach ($payouts as $payout) {
            $timestamp = $payout->paid_at ?? $payout->created_at;
            if (!$timestamp) {
                continue;
            }

            $bucketKey = $timestamp->format($bucketFormat);
            if (array_key_exists($bucketKey, $seriesMap)) {
                $seriesMap[$bucketKey] += (float) $payout->payout_amount;
            }
        }

        $earningsSeries = array_map(fn ($key) => round($seriesMap[$key], 2), $keys);
        $periodEarnings = (float) $payouts->sum('payout_amount');
        $completedPayouts = (int) $payouts->count();
        $averagePayout = $completedPayouts > 0 ? ($periodEarnings / $completedPayouts) : 0;

        $completedTrips = Trip::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $cancelledTrips = Trip::where('driver_id', $driver->id)
            ->where('status', 'cancelled')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $activeTrips = Trip::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->count();

        $topRoutes = $payouts
            ->groupBy(function ($payout) {
                $origin = $payout->trip->origin_dzongkhag ?? 'Unknown';
                $destination = $payout->trip->destination_dzongkhag ?? 'Unknown';

                return $origin . ' -> ' . $destination;
            })
            ->map(function ($routePayouts, $route) {
                return [
                    'route' => $route,
                    'earnings' => (float) $routePayouts->sum('payout_amount'),
                    'count' => $routePayouts->count(),
                ];
            })
            ->sortByDesc('earnings')
            ->take(5)
            ->values();

        return view('driver.reports', compact(
            'period',
            'labels',
            'earningsSeries',
            'periodEarnings',
            'completedPayouts',
            'averagePayout',
            'completedTrips',
            'cancelledTrips',
            'activeTrips',
            'topRoutes'
        ));
    }

    private function buildReportBuckets(string $period): array
    {
        $now = now();
        $labels = [];
        $keys = [];

        if ($period === 'daily') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
            $bucketFormat = 'Y-m-d H';

            for ($hour = 0; $hour < 24; $hour++) {
                $slot = $start->copy()->addHours($hour);
                $labels[] = $slot->format('H:00');
                $keys[] = $slot->format($bucketFormat);
            }
        } elseif ($period === 'weekly') {
            $start = $now->copy()->startOfDay()->subDays(6);
            $end = $now->copy()->endOfDay();
            $bucketFormat = 'Y-m-d';

            for ($day = 0; $day < 7; $day++) {
                $slot = $start->copy()->addDays($day);
                $labels[] = $slot->format('D');
                $keys[] = $slot->format($bucketFormat);
            }
        } elseif ($period === 'yearly') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            $bucketFormat = 'Y-m';

            for ($month = 1; $month <= 12; $month++) {
                $slot = $start->copy()->month($month);
                $labels[] = $slot->format('M');
                $keys[] = $slot->format($bucketFormat);
            }
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $bucketFormat = 'Y-m-d';
            $daysInMonth = (int) $start->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $slot = $start->copy()->day($day);
                $labels[] = $slot->format('d M');
                $keys[] = $slot->format($bucketFormat);
            }
        }

        return [$start, $end, $labels, $keys, $bucketFormat];
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
            'phone_number' => ['required', 'string', 'size:' . \App\Models\Setting::getPhoneNumberDigits(), 'regex:' . \App\Models\Setting::getPhoneNumberRegex(), 'unique:users,phone_number,' . $user->id],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'date_of_birth' => 'nullable|date|before:today',
            'vehicle_type' => 'required|string|max:50',
            'fuel_type' => 'required|in:Fuel,Electric',
            'years_of_experience' => 'nullable|integer|min:0|max:70',
            'public_age_range' => 'nullable|string|max:30',
            'show_experience_to_public' => 'nullable|in:1',
            'show_age_range_to_public' => 'nullable|in:1',
        ], [
            'phone_number.size' => \App\Models\Setting::getPhoneNumberHint(),
            'phone_number.regex' => \App\Models\Setting::getPhoneNumberHint(),
        ]);

        $userData = [
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ];

        if ($request->hasFile('profile_image')) {
            if (!empty($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $userData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user->update($userData);

        $driver->update([
            'date_of_birth' => $validated['date_of_birth'] ?? $driver->date_of_birth,
            'vehicle_type' => $validated['vehicle_type'],
            'fuel_type' => $validated['fuel_type'],
            'years_of_experience' => $validated['years_of_experience'] ?? $driver->years_of_experience,
            'public_age_range' => $validated['public_age_range'] ?? $driver->public_age_range,
            'show_experience_to_public' => $request->has('show_experience_to_public'),
            'show_age_range_to_public' => $request->has('show_age_range_to_public'),
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
