<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            // Save intended URL so we can redirect back after login
            session(['url.intended' => $request->fullUrl()]);

            return redirect()
                ->route('login')
                ->with('error', 'برای دسترسی به این صفحه باید وارد شوید.');
        }

        return $next($request);
    }
}