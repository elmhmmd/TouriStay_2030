<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
       
        if (!Auth::check()) {
            return $next($request);
        }
        
        
        $role = Auth::user()->role->name;
        
        
        $allowedPaths = [
            'admin' => ['admin', 'profile', 'types', 'annonces'],
            'tourist' => ['tourist', 'profile'],
            'proprietaire' => ['proprietaire', 'profile', 'annonces'],
        ];
        
        
        $dashboards = [
            'admin' => '/admin/dashboard',
            'tourist' => '/tourist/dashboard',
            'proprietaire' => '/proprietaire/dashboard',
        ];
        
       
        $authRoutes = ['login', 'register', 'logout'];
        if (in_array($request->path(), $authRoutes) || 
            $request->routeIs('login') || 
            $request->routeIs('register') || 
            $request->routeIs('logout')) {
            return $next($request);
        }
        
        
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            
            $segments = explode('/', $request->path());
            $basePath = $segments[0] ?? '';
            
            if (in_array($basePath, $allowedPaths[$role])) {
                return $next($request);
            }
        }
        
        
        $currentPath = $request->path();
        $roleAllowedPaths = $allowedPaths[$role] ?? [];
        
        $isAllowed = false;
        foreach ($roleAllowedPaths as $allowedPath) {
            if ($currentPath === $allowedPath || strpos($currentPath, $allowedPath . '/') === 0) {
                $isAllowed = true;
                break;
            }
        }
        
        
        if (!$isAllowed) {
            return redirect($dashboards[$role] ?? '/dashboard');
        }
        
        return $next($request);
    }
}