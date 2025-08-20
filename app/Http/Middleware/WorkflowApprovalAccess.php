<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WorkflowApprovalAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has workflow approval access (supervisor or HRD)
        if (!$user->is_supervisor && !$user->is_hrd) {
            abort(403, 'Anda tidak memiliki akses ke workflow approval.');
        }
        
        return $next($request);
    }
}
