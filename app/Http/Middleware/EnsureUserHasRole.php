<?php
//--//
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{

    // Roles: 0 = admin | 1 = customer | 2 = expert | 3 = company admin | 4 = company (owner)
    // Usage in routes: ->middleware('role:1')  or ->middleware('role:1,2')

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array((string) $user->role, $roles, true)) {
            abort(403, 'شما اجازه دسترسی به این صفحه را ندارید.');
        }

        return $next($request);
    }
}
