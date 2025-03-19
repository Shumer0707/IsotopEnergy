<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // dd([
        //     'gatherMiddleware' => request()->route()->gatherMiddleware(),
        //     'user' => $request->user(),
        //     'roles' => $roles,
        // ]);
        // dd($request->user());
        // dd($roles);

        if (empty($roles)) {
            abort(403, 'Unauthorized'); // Если роли не переданы, доступ запрещен
        }

        if ($request->user() && !in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
