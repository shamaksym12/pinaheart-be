<?php

namespace App\Http\Middleware;
use App\Events\Message\Logout;
use Closure;

class Activity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();            
            if ( ! $this->checkActivityLast24Hours($user)) {
                event(new Logout($user));
            }
            $user->setActivity();
            return $next($request);
        }        
    }

    public function checkActivityLast24Hours($user) 
    {
        if ($user->last_activity_at && $user->last_activity_at < now()->subHours(24)) {
            return false;
        }
        return true;
    }
}
