<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt login with credentials.
     * 
     * @param array $credentials
     * @return bool
     * @throws ValidationException
     */
    public function login(array $credentials): bool
    {
        if (Auth::attempt($credentials)) {
            request()->session()->regenerate();
            return true;
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Logout user.
     * 
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
