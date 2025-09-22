<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            return response()->json(['status' => 'ok'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}

