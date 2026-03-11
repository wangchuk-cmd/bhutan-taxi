<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Trip;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $dzongkhags = config('dzongkhags.list');
        $featuredTrips = Trip::with(['driver.user', 'route'])
            ->active()
            ->upcoming()
            ->where('available_seats', '>', 0)
            ->orderBy('departure_datetime')
            ->take(6)
            ->get();

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

        // Search trips by origin/destination with case-insensitive matching
        $trips = Trip::with(['driver.user', 'route'])
            ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
            ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
            ->active()
            ->whereDate('departure_datetime', $date)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_datetime')
            ->get();

        // Get route info if available for distance/time
        $route = Route::whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
            ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
            ->first();

        $dzongkhags = config('dzongkhags.list');

        return view('search-results', compact('trips', 'route', 'dzongkhags', 'validated'));
    }

    public function tripDetails($id)
    {
        $trip = Trip::with(['driver.user', 'route', 'bookings.passenger'])
            ->findOrFail($id);

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

        $trips = Trip::with(['driver.user', 'route'])
            ->whereRaw('LOWER(TRIM(origin_dzongkhag)) = ?', [strtolower($from)])
            ->whereRaw('LOWER(TRIM(destination_dzongkhag)) = ?', [strtolower($to)])
            ->active()
            ->whereDate('departure_datetime', $date)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_datetime')
            ->get();

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
