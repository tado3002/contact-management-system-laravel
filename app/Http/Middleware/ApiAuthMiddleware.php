<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = true;
        $token = $request->header('Authorization');

        if (!$token) {
            $authenticated = false;
        }

        $user = User::where('token', $token)->first();
        if (!$user) {
            $authenticated = false;
        }

        if ($authenticated) {
            Auth::login($user);
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => ["unauthorized"]
                ]
            ])->setStatusCode(401);
        }


        return $next($request);
    }
}
