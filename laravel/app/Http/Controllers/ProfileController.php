<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return "Thông tin cá nhân (Đang hoàn thiện ở Batch 3)";
    }

    public function update(Request $request)
    {
        return redirect()->back()->with('success', 'Đã cập nhật thông tin');
    }
}
