<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Mail\DriverRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isDriver()) {
                return redirect()->intended('/driver/dashboard');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'phone_number.regex' => 'Phone number must contain only digits.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'passenger',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Registration successful! Welcome aboard.');
    }

    public function showDriverRegister()
    {
        return view('auth.driver-register');
    }

    public function driverRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'license_number' => 'required|string|max:50|unique:drivers',
            'taxi_plate_number' => 'required|string|max:50|unique:drivers',
            'vehicle_type' => 'required|string|max:50',
            'fuel_type' => 'required|in:Fuel,Electric',
        ], [
            'phone_number.regex' => 'Phone number must contain only digits.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
        ]);

        $driver = Driver::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
            'taxi_plate_number' => $validated['taxi_plate_number'],
            'vehicle_type' => $validated['vehicle_type'],
            'fuel_type' => $validated['fuel_type'],
            'verified' => false,
            'active' => true,
        ]);

        // Send email notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new DriverRequestNotification($driver));
                } catch (\Exception $e) {
                    \Log::error('Failed to send driver registration notification to admin: ' . $e->getMessage());
                }
            }
        }

        Auth::login($user);

        return redirect('/driver/dashboard')->with('info', 'Registration successful! Please wait for admin approval before creating trips.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
