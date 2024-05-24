<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $activeUser = getAuthenticatedUser();
        
        if (Auth::guard('client')->check() && !Auth::guard('client')->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice'); // Customize this route
        }

        if(!(getAuthenticatedUser()->hasRole('Client'))){

            return $next($request);
        }else{

        if ($activeUser->plan_id == null && !(getAuthenticatedUser()->hasRole('admin'))) {
            return redirect()->route('plan.index');
        }
        if ($activeUser->plan_end_date == null  && !(getAuthenticatedUser()->hasRole('admin'))) {
            return redirect()->route('plan.index');
        }
        
        if($activeUser->plan_end_date < now()  && !(getAuthenticatedUser()->hasRole('admin'))){
            return redirect()->route('plan.index');
        }

        // Check if the 'web' guard (for users) email is verified
        if (Auth::guard('web')->check() && !Auth::guard('web')->user()->hasVerifiedEmail() && !(getAuthenticatedUser()->hasRole('admin'))) {
            return redirect()->route('verification.notice');
        }

        // Check if the 'client' guard (for clients) email is verified
       
    }
        return $next($request);
    }
}
