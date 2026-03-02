<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthWebController extends Controller
{
    /**
     * Show login page
     * GET /login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Logout — clears session and redirects to login
     * POST /logout
     * Note: actual token revocation happens via API call in JS
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}