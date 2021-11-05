<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use App\Providers\RouteServiceProvider;

class AuthorizeRole
{
  /**
       * Handle an incoming request.
       *
       * @param  \Illuminate\Http\Request  $request
       * @param  \Closure  $next
       * @param  string|null  role
       * @return mixed
       */
      public function handle($request, Closure $next, String $role = null)
      {

           if ($request->user()->isAdmin()) {
                return $next($request);
           }

           abort(403, 'Unauthorized');

      }
}
