<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateWhatsAppSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.wppconnect.secret');

        if (empty($secret)) {
            return response()->json(['error' => 'API secret not configured'], 500);
        }

        $provided = $request->header('X-Api-Secret');

        if (! hash_equals($secret, (string) $provided)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
