<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::where('role', 'passenger')->count(),
            'totalDrivers' => Driver::count(),
            'verifiedDrivers' => Driver::where('verified', true)->count(),
            'totalTrips' => Trip::count(),
            'activeTrips' => Trip::active()->upcoming()->count(),
            'totalBookings' => Booking::count(),
            'activeBookings' => Booking::active()->paid()->count(),
            'totalRevenue' => Payment::completed()->sum('amount'),
            'serviceCharges' => Payout::sum('service_charge'),
            'pendingPayouts' => Payout::pending()->sum('payout_amount'),
        ];

        $recentBookings = Booking::with(['passenger', 'trip.route'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $pendingDrivers = Driver::with('user')
            ->where('verified', false)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'pendingDrivers'));
    }

    // Financial Details Report
    public function financialDetails(Request $request)
    {
        $metric = $request->get('metric', 'revenue'); // revenue, charges, or payouts
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Default to last 30 days
        if (!$startDate) {
            $startDate = now()->subDays(30)->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = now()->format('Y-m-d');
        }

        // Initialize data based on metric type
        if ($metric === 'charges') {
            // Service Charges data
            $data = Payout::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            $totalAmount = $data->sum('service_charge');
            $count = $data->count();
            $avgAmount = $count > 0 ? $totalAmount / $count : 0;
            
            // Group by date
            $dailyData = $data->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map->sum('service_charge');
            
            $highestDay = $dailyData->max() ?? 0;
            $lowestDay = $dailyData->min() ?? 0;
            $avgDaily = $count > 0 ? $totalAmount / max(1, $dailyData->count()) : 0;
            
            $metricTitle = 'Service Charges Report';
            $metricLabel = 'Service Charges';
            
        } elseif ($metric === 'payouts') {
            // Pending Payouts data
            $data = Payout::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            $totalAmount = $data->sum('payout_amount');
            $count = $data->count();
            $avgAmount = $count > 0 ? $totalAmount / $count : 0;
            
            // Group by date
            $dailyData = $data->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map->sum('payout_amount');
            
            $highestDay = $dailyData->max() ?? 0;
            $lowestDay = $dailyData->min() ?? 0;
            $avgDaily = $count > 0 ? $totalAmount / max(1, $dailyData->count()) : 0;
            
            $metricTitle = 'Pending Payouts Report';
            $metricLabel = 'Pending Payouts';
            
        } else {
            // Revenue data (default)
            $data = Payment::completed()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();
            
            $totalAmount = $data->sum('amount');
            $count = $data->count();
            $avgAmount = $count > 0 ? $totalAmount / $count : 0;
            
            // Group by date
            $dailyData = $data->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map->sum('amount');
            
            $highestDay = $dailyData->max() ?? 0;
            $lowestDay = $dailyData->min() ?? 0;
            $avgDaily = $count > 0 ? $totalAmount / max(1, $dailyData->count()) : 0;
            
            $metricTitle = 'Total Revenue Report';
            $metricLabel = 'Total Revenue';
        }

        // Generate chart data based on period
        if ($period === 'daily') {
            $chartData = $this->generateDailyChartData($data);
        } elseif ($period === 'yearly') {
            $chartData = $this->generateYearlyChartData($data);
        } else {
            $chartData = $this->generateMonthlyChartData($data);
        }

        return view('admin.financial-details', compact(
            'totalAmount',
            'avgAmount',
            'count',
            'highestDay',
            'lowestDay',
            'avgDaily',
            'chartData',
            'period',
            'startDate',
            'endDate',
            'metric',
            'metricTitle',
            'metricLabel'
        ));
    }

    private function generateDailyChartData($payments)
    {
        $data = $payments->groupBy(function($payment) {
            return $payment->created_at->format('Y-m-d');
        })
        ->map->sum('amount')
        ->toArray();

        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }

    private function generateMonthlyChartData($payments)
    {
        $data = $payments->groupBy(function($payment) {
            return $payment->created_at->month;
        })
        ->map->sum('amount')
        ->toArray();

        $monthLabels = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $labels = [];
        $values = [];

        foreach ($data as $month => $amount) {
            $labels[] = $monthLabels[$month] ?? 'Month ' . $month;
            $values[] = $amount;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    private function generateYearlyChartData($payments)
    {
        $data = $payments->groupBy(function($payment) {
            return $payment->created_at->year;
        })
        ->map->sum('amount')
        ->toArray();

        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }

    // Route Management
    public function routes()
    {
        $routes = Route::withCount('trips')->orderBy('origin_dzongkhag')->paginate(15);
        return view('admin.routes.index', compact('routes'));
    }

    public function createRoute()
    {
        $dzongkhags = $this->getDzongkhags();
        return view('admin.routes.create', compact('dzongkhags'));
    }

    public function storeRoute(Request $request)
    {
        $validated = $request->validate([
            'origin_dzongkhag' => 'required|string|max:100',
            'destination_dzongkhag' => 'required|string|max:100|different:origin_dzongkhag',
            'distance_km' => 'required|numeric|min:1',
            'estimated_time' => 'required',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes')->with('success', 'Route created successfully!');
    }

    public function editRoute($id)
    {
        $route = Route::findOrFail($id);
        $dzongkhags = $this->getDzongkhags();
        return view('admin.routes.edit', compact('route', 'dzongkhags'));
    }

    public function updateRoute(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $validated = $request->validate([
            'origin_dzongkhag' => 'required|string|max:100',
            'destination_dzongkhag' => 'required|string|max:100|different:origin_dzongkhag',
            'distance_km' => 'required|numeric|min:1',
            'estimated_time' => 'required',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes')->with('success', 'Route updated successfully!');
    }

    public function deleteRoute($id)
    {
        $route = Route::findOrFail($id);

        if ($route->trips()->exists()) {
            return back()->with('error', 'Cannot delete route with existing trips.');
        }

        $route->delete();
        return redirect()->route('admin.routes')->with('success', 'Route deleted successfully!');
    }

    // Driver Management
    public function drivers()
    {
        $drivers = Driver::with('user')
            ->withCount('trips')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.drivers.index', compact('drivers'));
    }

    public function verifyDriver($id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        $driver->update(['verified' => true]);

        Notification::send(
            $driver->user_id,
            'admin',
            'Congratulations! Your driver account has been verified. You can now create trips.'
        );

        return back()->with('success', 'Driver verified successfully!');
    }

    public function toggleDriverStatus($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->update(['active' => !$driver->active]);

        $status = $driver->active ? 'activated' : 'deactivated';
        return back()->with('success', "Driver {$status} successfully!");
    }

    public function driverDetails($id)
    {
        $driver = Driver::with(['user', 'trips.route', 'payouts'])
            ->withCount('trips')
            ->findOrFail($id);

        $totalEarnings = $driver->payouts->sum('payout_amount');
        $pendingPayouts = $driver->payouts->where('status', 'pending')->sum('payout_amount');

        return view('admin.drivers.show', compact('driver', 'totalEarnings', 'pendingPayouts'));
    }

    // Trip Management
    public function trips()
    {
        $trips = Trip::with(['driver.user', 'route'])
            ->withCount('bookings')
            ->orderBy('departure_datetime', 'desc')
            ->paginate(15);

        return view('admin.trips.index', compact('trips'));
    }

    public function tripDetails($id)
    {
        $trip = Trip::with(['driver.user', 'route', 'bookings.passenger', 'bookings.payment', 'payout'])
            ->findOrFail($id);

        return view('admin.trips.show', compact('trip'));
    }

    public function cancelTrip($id)
    {
        $trip = Trip::with('bookings.passenger')->findOrFail($id);
        $trip->update(['status' => 'cancelled']);

        // Notify all passengers
        foreach ($trip->bookings as $booking) {
            if ($booking->passenger) {
                Notification::send(
                    $booking->passenger->id,
                    'alert',
                    'Your trip from ' . $trip->origin_dzongkhag . ' to ' . $trip->destination_dzongkhag . ' has been cancelled by admin.'
                );
            }
            $booking->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Trip cancelled successfully!');
    }

    // Booking Management
    public function bookings()
    {
        $bookings = Booking::with(['passenger', 'trip.route', 'trip.driver.user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function bookingDetails($id)
    {
        $booking = Booking::with(['passenger', 'trip.route', 'trip.driver.user', 'payment'])
            ->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    // Admin Booking on Behalf
    public function adminBookingSearch(Request $request)
    {
        $dzongkhags = config('dzongkhags.list');
        $trips = collect();
        $validated = [];
        $route = null;

        if ($request->has('from') && $request->has('to') && $request->has('date')) {
            $validated = $request->validate([
                'from' => 'required|string',
                'to' => 'required|string',
                'date' => 'required|date',
            ]);

            $from = trim($validated['from']);
            $to = trim($validated['to']);
            $date = $validated['date'];

            $route = \App\Models\Route::whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
                ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
                ->first();

            // Search trips regardless of route existence with case-insensitive matching
            $trips = Trip::with(['driver.user'])
                ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
                ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
                ->whereDate('departure_datetime', $date)
                ->where('status', 'active')
                ->where('available_seats', '>', 0)
                ->orderBy('departure_datetime')
                ->get();
        }

        return view('admin.bookings.search', compact('dzongkhags', 'trips', 'validated', 'route'));
    }

    public function adminBookingForm($tripId)
    {
        $trip = Trip::with(['driver.user'])
            ->where('status', 'active')
            ->where('available_seats', '>', 0)
            ->findOrFail($tripId);

        return view('admin.bookings.create', compact('trip'));
    }

    public function adminBooking(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'booking_type' => 'required|in:shared,full',
            'seats_booked' => 'required|integer|min:1',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.phone' => 'required|string|max:20|regex:/^[0-9]+$/',
        ], [
            'passengers.*.phone.regex' => 'Phone number must contain only digits.',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);
        $seatsNeeded = $validated['booking_type'] === 'full' ? $trip->total_seats : $validated['seats_booked'];

        if (!$trip->hasAvailableSeats($seatsNeeded)) {
            return back()->with('error', 'Not enough seats available.');
        }

        // Use first passenger as primary contact
        $primaryName = $validated['passengers'][0]['name'];
        $primaryPhone = $validated['passengers'][0]['phone'];

        // Check if passenger exists or create new
        $passenger = User::where('phone_number', $primaryPhone)->first();
        
        if (!$passenger) {
            $passenger = User::create([
                'name' => $primaryName,
                'phone_number' => $primaryPhone,
                'password' => Hash::make('changeme123'),
                'role' => 'passenger',
            ]);
        }

        $amount = $validated['booking_type'] === 'full' 
            ? $trip->full_taxi_price 
            : $trip->price_per_seat * $seatsNeeded;

        // Create pending booking and redirect to payment process page
        $booking = Booking::create([
            'trip_id' => $validated['trip_id'],
            'passenger_id' => $passenger->id,
            'passengers_info' => $validated['passengers'],
            'booking_type' => $validated['booking_type'],
            'seats_booked' => $seatsNeeded,
            'payment_status' => 'pending',
            'booking_time' => now(),
            'status' => 'pending',
        ]);

        // Store amount in session for payment processing
        session(['admin_booking_amount_' . $booking->id => $amount]);
        session(['admin_payment_start_' . $booking->id => now()]);

        return redirect()->route('admin.payment.process', $booking->id);
    }

    public function adminPaymentProcess(Booking $booking)
    {
        // Check if booking is pending
        if ($booking->payment_status !== 'pending') {
            return redirect()->route('admin.bookings')->with('error', 'Invalid booking status.');
        }

        $amount = session('admin_booking_amount_' . $booking->id, 0);
        
        // Configurable timeout from settings - same as passenger
        $timeRemaining = Setting::get('payment_timeout_seconds', 15);

        return view('admin.bookings.payment', compact('booking', 'amount', 'timeRemaining'));
    }

    public function adminPaymentComplete(Booking $booking)
    {
        if ($booking->payment_status !== 'pending') {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Payment already processed.']);
            }
            return redirect()->route('admin.bookings')->with('error', 'Payment already processed.');
        }

        $amount = session('admin_booking_amount_' . $booking->id, 0);
        $trip = $booking->trip;

        DB::transaction(function () use ($booking, $trip, $amount) {
            $booking->update([
                'payment_status' => 'paid',
                'payment_time' => now(),
                'status' => 'active',
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'status' => 'completed',
                'payment_method' => 'mock',
                'transaction_time' => now(),
            ]);

            $trip->decrement('available_seats', $booking->seats_booked);

            $serviceCharge = Payout::calculateServiceCharge($amount);
            Payout::create([
                'driver_id' => $trip->driver_id,
                'trip_id' => $trip->id,
                'total_amount' => $amount,
                'service_charge' => $serviceCharge,
                'payout_amount' => $amount - $serviceCharge,
                'status' => 'pending',
            ]);

            Notification::send(
                $booking->passenger_id,
                'booking',
                'Admin booked ' . $booking->seats_booked . ' seat(s) for you on ' . $trip->origin_dzongkhag . ' → ' . $trip->destination_dzongkhag
            );

            Notification::send(
                $trip->driver->user_id,
                'booking',
                'Admin booked ' . $booking->seats_booked . ' seat(s) on your trip.'
            );
        });

        // Clear session
        session()->forget(['admin_booking_amount_' . $booking->id, 'admin_payment_start_' . $booking->id]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.booking.receipt', $booking->id)
            ]);
        }

        return redirect()->route('admin.booking.receipt', $booking->id)->with('success', 'Payment completed successfully!');
    }

    public function adminPaymentTimeout(Booking $booking)
    {
        if ($booking->payment_status === 'pending') {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'expired',
            ]);
        }

        // Clear session
        session()->forget(['admin_booking_amount_' . $booking->id, 'admin_payment_start_' . $booking->id]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.bookings.create', ['tripId' => $booking->trip_id])
            ]);
        }

        return redirect()->route('admin.bookings.create', ['tripId' => $booking->trip_id])->with('error', 'Payment timeout. Booking cancelled.');
    }

    public function adminBookingReceipt(Booking $booking)
    {
        $payment = Payment::where('booking_id', $booking->id)
            ->where('status', 'completed')
            ->first();

        if (!$payment) {
            return redirect()->route('admin.bookings')->with('error', 'Payment not found.');
        }

        return view('admin.bookings.receipt', compact('booking', 'payment'));
    }

    // Payout Management
    public function payouts()
    {
        $payouts = Payout::with(['driver.user', 'trip.route'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'totalPending' => Payout::pending()->sum('payout_amount'),
            'totalPaid' => Payout::where('status', 'completed')->sum('payout_amount'),
            'totalServiceCharges' => Payout::sum('service_charge'),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    public function processPayout($id)
    {
        $payout = Payout::with('driver.user')->findOrFail($id);
        $payout->update(['status' => 'completed', 'paid_at' => now()]);

        Notification::send(
            $payout->driver->user_id,
            'payment',
            'Payout of Nu. ' . number_format($payout->payout_amount, 2) . ' has been processed.'
        );

        return back()->with('success', 'Payout processed successfully!');
    }

    public function processAllPayouts()
    {
        $pendingPayouts = Payout::with('driver.user')->pending()->get();

        foreach ($pendingPayouts as $payout) {
            $payout->update(['status' => 'completed', 'paid_at' => now()]);

            Notification::send(
                $payout->driver->user_id,
                'payment',
                'Payout of Nu. ' . number_format($payout->payout_amount, 2) . ' has been processed.'
            );
        }

        return back()->with('success', 'All pending payouts processed successfully!');
    }

    // Complaints Management
    public function complaints()
    {
        $complaints = Complaint::with(['user', 'trip'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function resolveComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update(['status' => 'resolved']);

        Notification::send(
            $complaint->user_id,
            'admin',
            'Your ' . $complaint->type . ' regarding "' . $complaint->subject . '" has been resolved.'
        );

        return back()->with('success', 'Complaint resolved!');
    }

    public function respondComplaint(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'required|string|max:2000',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->update([
            'admin_response' => $request->admin_response,
            'status' => 'resolved',
        ]);

        Notification::send(
            $complaint->user_id,
            'admin',
            'Your ' . $complaint->type . ' regarding "' . $complaint->subject . '" has been resolved. Admin response: ' . $request->admin_response
        );

        return back()->with('success', 'Response sent and marked as resolved!');
    }

    // Users Management
    public function users()
    {
        $users = User::where('role', 'passenger')
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function updateUserRole($id, Request $request)
    {
        $request->validate(['role' => 'required|in:passenger,driver,admin']);
        
        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);
        
        return back()->with('success', "User role updated to {$request->role}!");
    }

    // Reports
    public function reports(Request $request)
    {
        $locations = config('dzongkhags.list');
        $drivers = Driver::with('user')->get();

        return view('admin.reports.index', compact('locations', 'drivers'));
    }

    // Helper method to apply date filters properly
    private function applyDateFilter($query, Request $request, $dateColumn)
    {
        // If specific_dates is provided (comma-separated days like "7,9,10")
        if ($request->filled('specific_dates')) {
            $days = array_map('intval', array_filter(explode(',', $request->specific_dates)));
            if (!empty($days)) {
                // Get year and month from date_from or current
                $baseDate = $request->filled('date_from') ? $request->date_from : now()->format('Y-m');
                $yearMonth = substr($baseDate, 0, 7); // "2026-03"
                
                $dates = array_map(fn($day) => $yearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT), $days);
                $query->whereIn(\DB::raw("DATE($dateColumn)"), $dates);
                return $query;
            }
        }
        
        // If only date_from is filled (no date_to) - exact date match
        if ($request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereDate($dateColumn, '=', $request->date_from);
        }
        // If both dates filled - range inclusive
        elseif ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate($dateColumn, '>=', $request->date_from)
                  ->whereDate($dateColumn, '<=', $request->date_to);
        }
        
        return $query;
    }

    // Search endpoints for reports
    public function searchTrips(Request $request)
    {
        $query = Trip::with(['driver.user']);

        $this->applyDateFilter($query, $request, 'departure_datetime');
        if ($request->filled('origin')) {
            $query->where('origin_dzongkhag', $request->origin);
        }
        if ($request->filled('destination')) {
            $query->where('destination_dzongkhag', $request->destination);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trips = $query->orderBy('departure_datetime', 'desc')->limit(500)->get();

        return response()->json($trips->map(fn($t) => [
            'id' => $t->id,
            'driver_name' => $t->driver->user->name ?? 'Unknown',
            'origin' => $t->origin_dzongkhag,
            'destination' => $t->destination_dzongkhag,
            'departure' => $t->departure_datetime->format('Y-m-d H:i'),
            'total_seats' => $t->total_seats,
            'available_seats' => $t->available_seats,
            'price_per_seat' => number_format($t->price_per_seat),
            'status' => $t->status,
        ]));
    }

    public function searchBookings(Request $request)
    {
        $query = Booking::with(['passenger', 'trip']);

        $this->applyDateFilter($query, $request, 'created_at');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('passenger', fn($q) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
            );
        }

        $bookings = $query->orderBy('created_at', 'desc')->limit(500)->get();

        return response()->json($bookings->map(fn($b) => [
            'id' => $b->id,
            'passenger_name' => $b->passenger->name ?? 'Unknown',
            'passenger_phone' => $b->passenger->phone_number ?? '',
            'route' => ($b->trip->origin_dzongkhag ?? '') . ' → ' . ($b->trip->destination_dzongkhag ?? ''),
            'departure' => $b->trip?->departure_datetime?->format('Y-m-d H:i') ?? 'N/A',
            'seats' => $b->seats_booked,
            'amount' => number_format($b->total_amount),
            'status' => $b->status,
            'payment_status' => $b->payment_status,
        ]));
    }

    public function searchPayments(Request $request)
    {
        $query = Payment::with('booking');

        $this->applyDateFilter($query, $request, 'created_at');
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('transaction_id', 'like', "%{$request->search}%");
        }

        $payments = $query->orderBy('created_at', 'desc')->limit(500)->get();

        return response()->json($payments->map(fn($p) => [
            'id' => $p->id,
            'booking_id' => $p->booking_id,
            'amount' => number_format($p->amount),
            'method' => strtoupper($p->payment_method ?? 'N/A'),
            'txn_id' => $p->transaction_id,
            'status' => $p->status,
            'date' => $p->created_at->format('Y-m-d H:i'),
        ]));
    }

    public function searchDrivers(Request $request)
    {
        $query = Driver::with('user');

        if ($request->filled('verified')) {
            $query->where('verified', $request->verified);
        }
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                  ->orWhere('taxi_plate_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => 
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                  );
            });
        }

        $drivers = $query->orderBy('created_at', 'desc')->limit(500)->get();

        return response()->json($drivers->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->user->name ?? 'Unknown',
            'phone' => $d->user->phone_number ?? '',
            'license' => $d->license_number,
            'vehicle' => ucfirst($d->vehicle_type ?? 'N/A') . ' - ' . $d->taxi_plate_number,
            'verified' => $d->verified,
            'active' => $d->active,
        ]));
    }

    public function searchPayouts(Request $request)
    {
        $query = Payout::with(['driver.user', 'trip']);

        $this->applyDateFilter($query, $request, 'created_at');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $payouts = $query->orderBy('created_at', 'desc')->limit(500)->get();

        return response()->json($payouts->map(fn($p) => [
            'id' => $p->id,
            'driver_name' => $p->driver->user->name ?? 'Unknown',
            'trip_route' => ($p->trip->origin_dzongkhag ?? '') . ' → ' . ($p->trip->destination_dzongkhag ?? ''),
            'total' => number_format($p->total_amount),
            'charge' => number_format($p->service_charge),
            'payout' => number_format($p->payout_amount),
            'status' => $p->status,
            'date' => $p->created_at->format('Y-m-d H:i'),
        ]));
    }

    public function searchRefunds(Request $request)
    {
        $query = Booking::with(['passenger', 'trip'])
            ->where('status', 'cancelled')
            ->where('refund_status', '!=', 'none');

        $this->applyDateFilter($query, $request, 'cancellation_time');
        
        if ($request->filled('refund_status')) {
            $query->where('refund_status', $request->refund_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('passenger', fn($p) => 
                      $p->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                  );
            });
        }

        $refunds = $query->orderBy('cancellation_time', 'desc')->limit(500)->get();

        return response()->json($refunds->map(fn($b) => [
            'id' => $b->id,
            'passenger_name' => $b->passenger->name ?? 'Unknown',
            'passenger_phone' => $b->passenger->phone_number ?? '',
            'route' => ($b->trip->origin_dzongkhag ?? '') . ' → ' . ($b->trip->destination_dzongkhag ?? ''),
            'amount' => number_format($b->total_amount ?? 0),
            'cancelled_at' => $b->cancellation_time?->format('Y-m-d H:i') ?? 'N/A',
            'refund_status' => $b->refund_status,
        ]));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($request->has('status')) {
            $booking->status = $request->status;
        }
        if ($request->has('payment_status')) {
            $booking->payment_status = $request->payment_status;
        }
        $booking->save();

        return response()->json(['success' => true, 'message' => 'Booking updated successfully']);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = $request->status;
        $payment->save();

        // Also update booking payment status if payment is completed
        if ($request->status === 'completed' && $payment->booking) {
            $payment->booking->update(['payment_status' => 'paid', 'payment_time' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Payment updated successfully']);
    }

    public function updateRefundStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->refund_status = $request->refund_status;
        $booking->save();

        return response()->json(['success' => true, 'message' => 'Refund status updated successfully']);
    }

    public function updatePayoutStatus(Request $request, $id)
    {
        $payout = Payout::findOrFail($id);
        $payout->status = $request->status;
        if ($request->status === 'completed') {
            $payout->paid_at = now();
        }
        $payout->save();

        return response()->json(['success' => true, 'message' => 'Payout updated successfully']);
    }

    public function exportTrips(Request $request)
    {
        $query = Trip::with(['driver.user']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('departure_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('departure_datetime', '<=', $request->date_to);
        }
        if ($request->filled('origin')) {
            $query->where('origin_dzongkhag', $request->origin);
        }
        if ($request->filled('destination')) {
            $query->where('destination_dzongkhag', $request->destination);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trips = $query->orderBy('departure_datetime', 'desc')->get();

        $filename = 'trips_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($trips) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Driver', 'Phone', 'Origin', 'Destination', 'Departure Date', 'Total Seats', 'Available Seats', 'Price/Seat', 'Full Taxi Price', 'Status', 'Created At']);

            foreach ($trips as $trip) {
                fputcsv($file, [
                    $trip->id,
                    $trip->driver->user->name ?? 'N/A',
                    $trip->driver->user->phone_number ?? 'N/A',
                    $trip->origin_dzongkhag,
                    $trip->destination_dzongkhag,
                    $trip->departure_datetime->format('Y-m-d H:i'),
                    $trip->total_seats,
                    $trip->available_seats,
                    $trip->price_per_seat,
                    $trip->full_taxi_price,
                    $trip->status,
                    $trip->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBookings(Request $request)
    {
        $query = Booking::with(['trip', 'user', 'payment']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Passenger', 'Phone', 'Route', 'Departure Date', 'Booking Type', 'Seats', 'Payment Status', 'Amount', 'Status', 'Booked At']);

            foreach ($bookings as $booking) {
                $passenger = $booking->passengers_info[0] ?? [];
                fputcsv($file, [
                    $booking->id,
                    $passenger['name'] ?? $booking->user->name ?? 'N/A',
                    $passenger['phone'] ?? $booking->user->phone_number ?? 'N/A',
                    ($booking->trip->origin_dzongkhag ?? 'N/A') . ' → ' . ($booking->trip->destination_dzongkhag ?? 'N/A'),
                    $booking->trip->departure_datetime?->format('Y-m-d H:i') ?? 'N/A',
                    ucfirst($booking->booking_type),
                    $booking->seats_booked,
                    ucfirst($booking->payment_status),
                    $booking->payment->amount ?? 0,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPayments(Request $request)
    {
        $query = Payment::with(['booking.trip', 'booking.user']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Transaction ID', 'Booking ID', 'Passenger', 'Route', 'Amount', 'Method', 'Status', 'Payment Date']);

            foreach ($payments as $payment) {
                $passenger = $payment->booking->passengers_info[0] ?? [];
                fputcsv($file, [
                    $payment->id,
                    $payment->transaction_id ?? 'N/A',
                    $payment->booking_id,
                    $passenger['name'] ?? $payment->booking->user->name ?? 'N/A',
                    ($payment->booking->trip->origin_dzongkhag ?? 'N/A') . ' → ' . ($payment->booking->trip->destination_dzongkhag ?? 'N/A'),
                    $payment->amount,
                    ucfirst($payment->payment_method ?? 'N/A'),
                    ucfirst($payment->status),
                    $payment->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDrivers(Request $request)
    {
        $query = Driver::with(['user', 'payouts']);

        // Apply filters
        if ($request->filled('verified')) {
            $query->where('verified', $request->verified === 'yes');
        }
        if ($request->filled('active')) {
            $query->where('active', $request->active === 'yes');
        }

        $drivers = $query->get();

        $filename = 'drivers_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($drivers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone', 'Email', 'License No', 'Vehicle No', 'Vehicle Type', 'Verified', 'Active', 'Total Earnings', 'Total Trips', 'Joined At']);

            foreach ($drivers as $driver) {
                $totalEarnings = $driver->payouts->where('status', 'completed')->sum('payout_amount');
                $totalTrips = $driver->trips()->count();
                fputcsv($file, [
                    $driver->id,
                    $driver->user->name ?? 'N/A',
                    $driver->user->phone_number ?? 'N/A',
                    $driver->user->email ?? 'N/A',
                    $driver->license_number ?? 'N/A',
                    $driver->taxi_plate_number ?? $driver->vehicle_number ?? 'N/A',
                    $driver->vehicle_type ?? 'N/A',
                    $driver->verified ? 'Yes' : 'No',
                    $driver->active ? 'Yes' : 'No',
                    $totalEarnings,
                    $totalTrips,
                    $driver->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPayouts(Request $request)
    {
        $query = Payout::with(['driver.user', 'trip']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $payouts = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payouts_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($payouts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Driver', 'Phone', 'Trip Route', 'Total Amount', 'Service Charge', 'Payout Amount', 'Status', 'Created At', 'Paid At']);

            foreach ($payouts as $payout) {
                fputcsv($file, [
                    $payout->id,
                    $payout->driver->user->name ?? 'N/A',
                    $payout->driver->user->phone_number ?? 'N/A',
                    ($payout->trip->origin_dzongkhag ?? 'N/A') . ' → ' . ($payout->trip->destination_dzongkhag ?? 'N/A'),
                    $payout->total_amount,
                    $payout->service_charge,
                    $payout->payout_amount,
                    ucfirst($payout->status),
                    $payout->created_at->format('Y-m-d H:i'),
                    $payout->paid_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Admin Trip CRUD
    public function createTrip()
    {
        $locations = config('dzongkhags.list');
        $drivers = Driver::with('user')->where('verified', true)->where('active', true)->get();
        return view('admin.trips.create', compact('locations', 'drivers'));
    }

    public function storeTrip(Request $request)
    {
        $locations = config('dzongkhags.list');

        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'origin_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations)],
            'destination_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations), 'different:origin_dzongkhag'],
            'departure_datetime' => 'required|date|after:now',
            'total_seats' => 'required|integer|min:1|max:12',
            'price_per_seat' => 'required|numeric|min:0',
            'full_taxi_price' => 'required|numeric|min:0',
        ]);

        $route = Route::where('origin_dzongkhag', $validated['origin_dzongkhag'])
            ->where('destination_dzongkhag', $validated['destination_dzongkhag'])
            ->first();

        Trip::create([
            'driver_id' => $validated['driver_id'],
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

        return redirect()->route('admin.trips')->with('success', 'Trip created successfully!');
    }

    public function editTrip($id)
    {
        $trip = Trip::with('driver.user')->findOrFail($id);
        $locations = config('dzongkhags.list');
        $drivers = Driver::with('user')->where('verified', true)->where('active', true)->get();
        return view('admin.trips.edit', compact('trip', 'locations', 'drivers'));
    }

    public function updateTrip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $locations = config('dzongkhags.list');

        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'origin_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations)],
            'destination_dzongkhag' => ['required', 'string', 'in:' . implode(',', $locations), 'different:origin_dzongkhag'],
            'departure_datetime' => 'required|date|after:now',
            'total_seats' => 'required|integer|min:' . ($trip->total_seats - $trip->available_seats),
            'price_per_seat' => 'required|numeric|min:0',
            'full_taxi_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $route = Route::where('origin_dzongkhag', $validated['origin_dzongkhag'])
            ->where('destination_dzongkhag', $validated['destination_dzongkhag'])
            ->first();

        $bookedSeats = $trip->total_seats - $trip->available_seats;
        $newAvailableSeats = $validated['total_seats'] - $bookedSeats;

        $trip->update([
            'driver_id' => $validated['driver_id'],
            'route_id' => $route?->id,
            'origin_dzongkhag' => $validated['origin_dzongkhag'],
            'destination_dzongkhag' => $validated['destination_dzongkhag'],
            'departure_datetime' => $validated['departure_datetime'],
            'total_seats' => $validated['total_seats'],
            'available_seats' => $newAvailableSeats,
            'price_per_seat' => $validated['price_per_seat'],
            'full_taxi_price' => $validated['full_taxi_price'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.trips')->with('success', 'Trip updated successfully!');
    }

    public function deleteTrip($id)
    {
        $trip = Trip::withCount('bookings')->findOrFail($id);
        
        if ($trip->bookings_count > 0) {
            return back()->with('error', 'Cannot delete trip with existing bookings.');
        }

        $trip->delete();
        return redirect()->route('admin.trips')->with('success', 'Trip deleted successfully!');
    }

    private function getDzongkhags()
    {
        return [
            'Bumthang', 'Chhukha', 'Dagana', 'Gasa', 'Haa',
            'Lhuentse', 'Mongar', 'Paro', 'Pemagatshel', 'Punakha',
            'Samdrup Jongkhar', 'Samtse', 'Sarpang', 'Thimphu', 'Trashigang',
            'Trashiyangtse', 'Trongsa', 'Tsirang', 'Wangdue Phodrang', 'Zhemgang'
        ];
    }

    // Settings Management
    public function settings()
    {
        $settings = [
            'service_charge_percentage' => Setting::get('service_charge_percentage', 10),
            'min_booking_hours'         => Setting::get('min_booking_hours', 2),
            'max_seats_per_booking'     => Setting::get('max_seats_per_booking', 4),
            'payment_timeout_seconds'   => Setting::get('payment_timeout_seconds', 15),
            'site_name'                 => Setting::get('site_name', 'Bhutan Taxi'),
            'contact_email'             => Setting::get('contact_email', ''),
            'contact_phone'             => Setting::get('contact_phone', ''),
            'driver_payout_time'        => Setting::get('driver_payout_time', '24'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'service_charge_percentage' => 'required|numeric|min:0|max:100',
            'min_booking_hours'         => 'required|integer|min:0|max:48',
            'max_seats_per_booking'     => 'required|integer|min:1|max:12',
            'payment_timeout_minutes'   => 'required|integer|min:1|max:15',
            'site_name'                 => 'required|string|max:100',
            'contact_email'             => 'nullable|email|max:255',
            'contact_phone'             => 'nullable|string|max:20',
            'driver_payout_time'        => 'required|in:immediate,24,48,72',
        ]);

        Setting::set('service_charge_percentage', $validated['service_charge_percentage'], 'decimal',  'Service charge percentage for driver payouts');
        Setting::set('min_booking_hours',         $validated['min_booking_hours'],         'integer',  'Minimum hours before departure for booking');
        Setting::set('max_seats_per_booking',     $validated['max_seats_per_booking'],     'integer',  'Maximum seats per booking');
        Setting::set('payment_timeout_seconds', $validated['payment_timeout_minutes'] * 60, 'integer', 'Seconds for payment confirmation countdown');
        Setting::set('site_name',                 $validated['site_name'],                 'string',   'Site name');
        Setting::set('contact_email',             $validated['contact_email'] ?? '',       'string',   'Contact email');
        Setting::set('contact_phone',             $validated['contact_phone'] ?? '',       'string',   'Contact phone');
        Setting::set('driver_payout_time',        $validated['driver_payout_time'],        'string',   'Driver payout time (immediate or hours)');

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    public function updatePayoutSettings(Request $request)
    {
        $request->validate([
            'driver_payout_time' => 'required|in:immediate,24,48,72',
        ]);

        // Save payout time setting
        Setting::set('driver_payout_time', $request->input('driver_payout_time'), 'string', 'Driver payout time (immediate or hours)');

        return redirect()->back()->with('success', 'Payout settings updated successfully.');
    }
}

