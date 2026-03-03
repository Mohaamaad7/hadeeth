<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * الاستخدام: ->middleware('role:admin') أو ->middleware('role:admin,reviewer')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // الأدمن يدخل كل مكان
        if ($user->isAdmin()) {
            return $next($request);
        }

        // التحقق من الدور
        if (!in_array($user->role, $roles)) {
            abort(403, 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        return $next($request);
    }
}
