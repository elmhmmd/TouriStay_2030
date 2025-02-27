<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !in_array($request->path(), ['admin/dashboard', 'tourist/dashboard', 'proprietaire/dashboard'])){
            $role = Auth::user()->role->name;
            $redirectTo = match ($role) {
                'admin' => '/admin/dashboard',
                'tourist' => '/tourist/dashboard',
                'proprietaire' => '/proprietaire/dashboard',
                default => '/dashboard',
            };
            return redirect($redirectTo);
        }

        return $next($request);
    }
}