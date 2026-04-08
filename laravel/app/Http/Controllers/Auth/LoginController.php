<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return "Trang Đăng nhập (Đang hoàn thiện ở Batch 3)";
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        return redirect()->back()->withErrors(['error' => 'Chức năng đang được xây dựng']);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        auth()->logout();
        return redirect('/');
    }
}
