<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function unauthenticated($request,  $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
