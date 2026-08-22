<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

final class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'google_id' => $googleUser->getId(),
            ], [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Str::password(12),
                'email_verified_at' => now(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return view('auth.google-popup-callback', [
                'status' => 'success',
                'message' => null,
            ]);
        } catch (Throwable $e) {
            report($e);

            return view('auth.google-popup-callback', [
                'status' => 'error',
                'message' => 'Google authentication failed. Please try again.',
            ]);
        }
    }
}