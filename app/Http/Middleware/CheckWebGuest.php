<?php
// ── app/Http/Middleware/CheckWebGuest.php ─────────────────────────
// Redirects already logged in users away from login page

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebGuest
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('logged_in')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}