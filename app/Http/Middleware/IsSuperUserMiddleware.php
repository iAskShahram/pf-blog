<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IsSuperUserMiddleware
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
		// check if the user is super user
		if (!$request->user()->isSuperUser()) {
			return response()->json(['error' => 'You are not authorized to access this route'], 403);
		}
		return $next($request);
	}
}
