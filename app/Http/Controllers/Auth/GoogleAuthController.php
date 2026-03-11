<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists by email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                
                // Log the user in
                Auth::login($user, true);
                
                // Redirect based on role
                return $this->redirectUserByRole($user);
            }
            
            // Create new user
            // Determine a phone number placeholder if Google didn't provide one
            // the users table currently requires a unique, non-null phone_number so
            // we generate a stable fallback to avoid database errors. We could also
            // make the column nullable in a future migration instead.
            $phone = $googleUser->phone_number ?: 'google_'.$googleUser->id;

            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'phone_number' => $phone,
                'password' => Hash::make(Str::random(24)), // Random password for Google users
                'role' => 'passenger',
                'email_verified_at' => now(), // Google email is already verified
            ]);
            
            // Log the user in
            Auth::login($user, true);
            
            return redirect('/')->with('success', 'Welcome to Bhutan Taxi! Your account has been created with Google.');
            
        } catch (Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again or use email/password login.');
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectUserByRole($user)
    {
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard')->with('success', 'Welcome back, Admin!');
        } elseif ($user->isDriver()) {
            return redirect('/driver/dashboard')->with('success', 'Welcome back, Driver!');
        }
        
        return redirect('/')->with('success', 'Welcome back!');
    }
}
