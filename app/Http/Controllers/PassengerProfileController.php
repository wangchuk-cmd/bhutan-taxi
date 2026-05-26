<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PassengerProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if (!$user->isPassenger()) {
            return redirect()->route('home')->with('error', 'This profile page is only for passenger accounts.');
        }

        return view('passenger.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPassenger()) {
            return redirect()->route('home')->with('error', 'This profile update is only for passenger accounts.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/|unique:users,phone_number,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|string',
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)],
        ], [
            'phone_number.regex' => 'Phone number must contain only digits.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
        ];

        if ($request->hasFile('profile_image')) {
            if (!empty($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $userData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return back()->withErrors(['current_password' => 'Current password is required to set a new password.'])->withInput();
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }

            $userData['password'] = $validated['password'];
        }

        $user->update($userData);

        return back()->with('success', 'Profile updated successfully.');
    }
}
