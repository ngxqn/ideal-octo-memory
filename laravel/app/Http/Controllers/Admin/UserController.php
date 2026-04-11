<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request) { /* Implementation in Batch 6.7 */ }
    public function update(Request $request, User $user) { /* Implementation in Batch 6.7 */ }
}
