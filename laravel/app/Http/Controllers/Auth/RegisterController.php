<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return "Trang Đăng ký (Đang hoàn thiện ở Batch 3)";
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        return redirect()->back()->withErrors(['error' => 'Chức năng đang được xây dựng']);
    }
}
