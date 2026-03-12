<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache featured trips for 10 minutes
        $cacheKey = 'featured_trips_' . now()->format('Y-m-d_H_i');
        $featuredTrips = Cache::remember($cacheKey, 600, function () {
            return Trip::select(['id', 'driver_id', 'route_id', 'origin_dzongkhag', 'destination_dzongkhag', 
                               'departure_datetime', 'total_seats', 'available_seats', 'price_per_seat', 'status'])
                ->with(['driver:id,user_id', 'driver.user:id,name,profile_picture', 'route:id,origin_dzongkhag,destination_dzongkhag,distance_km,estimated_duration'])
                ->active()
                ->upcoming()
                ->where('available_seats', '>', 0)
                ->orderBy('departure_datetime')
                ->take(6)
                ->get();
        });

        $dzongkhags = Cache::remember('dzongkhags.list', 86400, function () {
            return config('dzongkhags.list');
        });

        return view('home', compact('dzongkhags', 'featuredTrips'));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'required|date',
        ]);

        $from = trim($validated['from']);
        $to = trim($validated['to']);
        $date = $validated['date'];

        // Cache search results for 5 minutes per search query
        $cacheKey = 'trip_search_' . md5($from . $to . $date);
        $trips = Cache::remember($cacheKey, 300, function () use ($from, $to, $date) {
            return Trip::select(['id', 'driver_id', 'route_id', 'origin_dzongkhag', 'destination_dzongkhag', 
                               'departure_datetime', 'total_seats', 'available_seats', 'price_per_seat', 'status'])
                ->with(['driver:id,user_id', 'driver.user:id,name,profile_picture', 'route:id,origin_dzongkhag,destination_dzongkhag,distance_km,estimated_duration'])
                ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
                ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
                ->active()
                ->whereDate('departure_datetime', $date)
                ->where('available_seats', '>', 0)
                ->orderBy('departure_datetime')
                ->get();
        });

        // Get route info if available for distance/time
        $route = Cache::remember('route_' . md5($from . $to), 86400, function () use ($from, $to) {
            return Route::select(['id', 'origin_dzongkhag', 'destination_dzongkhag', 'distance_km', 'estimated_duration'])
                ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
                ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
                ->first();
        });

        $dzongkhags = Cache::remember('dzongkhags.list', 86400, function () {
            return config('dzongkhags.list');
        });

        return view('search-results', compact('trips', 'route', 'dzongkhags', 'validated'));
    }

    public function tripDetails($id)
    {
        // Cache individual trip details for 10 minutes
        $trip = Cache::remember('trip_' . $id, 600, function () use ($id) {
            return Trip::select(['id', 'driver_id', 'route_id', 'origin_dzongkhag', 'destination_dzongkhag', 
                               'departure_datetime', 'total_seats', 'available_seats', 'price_per_seat', 'status'])
                ->with(['driver:id,user_id', 'driver.user:id,name,profile_picture', 'route:id,origin_dzongkhag,destination_dzongkhag,distance_km,estimated_duration', 'bookings:id,trip_id,passenger_id'])
                ->findOrFail($id);
        });

        return view('trip-details', compact('trip'));
    }

    /**
     * API endpoint for real-time trip search updates
     */
    public function apiSearchTrips(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'required|date',
        ]);

        $from = trim($validated['from']);
        $to = trim($validated['to']);
        $date = $validated['date'];

        // Cache search results for 3 minutes for API calls
        $cacheKey = 'api_trip_search_' . md5($from . $to . $date);
        $trips = Cache::remember($cacheKey, 180, function () use ($from, $to, $date) {
            return Trip::select(['id', 'driver_id', 'route_id', 'origin_dzongkhag', 'destination_dzongkhag', 
                               'departure_datetime', 'total_seats', 'available_seats', 'price_per_seat', 'status'])
                ->with(['driver:id,user_id', 'driver.user:id,name,profile_picture', 'route:id,origin_dzongkhag,destination_dzongkhag,distance_km,estimated_duration'])
                ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
                ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
                ->active()
                ->whereDate('departure_datetime', $date)
                ->where('available_seats', '>', 0)
                ->orderBy('departure_datetime')
                ->get();
        });

        // Render the trips list partial
        $html = view('partials.trips-list', compact('trips'))->render();

        return response()->json([
            'success' => true,
            'count' => $trips->count(),
            'trips' => $trips,
            'html' => $html
        ]);
    }
}
