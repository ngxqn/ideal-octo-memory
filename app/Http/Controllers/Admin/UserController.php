<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users with stats and filtering.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Keyword Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('username', 'like', "%$s%")
                  ->orWhere('full_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        // Role Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status Filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Calculate Stats
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)->count();
        $lockedUsers = User::where('is_active', 0)->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'lockedUsers'));
    }

    /**
     * Create a new user account via AJAX.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        // Ensure optional address fields are empty strings instead of null 
        // to satisfy DB NOT NULL constraints
        $data['address'] = $data['address'] ?? '';
        $data['commune'] = $data['commune'] ?? '';
        $data['city'] = $data['city'] ?? '';

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tài khoản người dùng đã được tạo thành công.',
            'user' => $user
        ]);
    }

    /**
     * Toggle the active status (Lock/Unlock) via AJAX.
     */
    public function toggleActive(User $user)
    {
        // Self-Lock Guard: Prevent locking current authenticated admin
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự khóa tài khoản của chính mình.'
            ], 400);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'mở khóa' : 'khóa';

        return response()->json([
            'success' => true,
            'message' => "Đã {$statusText} tài khoản người dùng thành công.",
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Reset user password via AJAX.
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user)
    {
        // Password hashing is handled by User model cast 'password' => 'hashed'
        $user->password = $request->password;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được cập nhật thành công.'
        ]);
    }
}
