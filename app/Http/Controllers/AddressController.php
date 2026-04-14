<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;

        $address = DB::transaction(function () use ($user, $data) {
            if (!empty($data['is_default'])) {
                $user->addresses()->update(['is_default' => false]);
            }

            return UserAddress::create($data);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được thêm thành công.',
                'address' => $address
            ]);
        }

        return back()->with('success', 'Địa chỉ đã được thêm thành công.');
    }

    /**
     * Set the specified address as default.
     */
    public function setDefault(Request $request, UserAddress $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Đã đặt địa chỉ làm mặc định.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Request $request, UserAddress $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được xóa.'
            ]);
        }

        return back()->with('success', 'Địa chỉ đã được xóa thành công.');
    }
}
