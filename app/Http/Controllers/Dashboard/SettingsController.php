<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('dashboard.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $request->user()->update(['password' => $request->password]);

        Auth::login($request->user());

        return back()->with('success', 'Password berhasil diubah.');
    }
}
