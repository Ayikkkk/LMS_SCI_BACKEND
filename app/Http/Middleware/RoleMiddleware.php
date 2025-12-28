<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $roleValue)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('teacher.login');
        }

        // Jika role guru = 1
        if ($roleValue === 'teacher' && $user->role != 1) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
