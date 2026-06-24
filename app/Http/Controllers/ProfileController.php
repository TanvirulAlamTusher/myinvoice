<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
      // =======================
    // PROFILE VIEW
    // =======================
    public function index()
    {
        $user = Auth::user();

        return view('profile.index', compact('user'));
    }

       // =======================
    // UPDATE PROFILE (AJAX READY)
    // =======================
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:4|confirmed',
        ]);

        $user->name  = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // AJAX response support
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully');
    }
public function changePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:4|confirmed',
    ]);

    try {

        $user = Auth::user();

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ], 500);
    }
}
}
