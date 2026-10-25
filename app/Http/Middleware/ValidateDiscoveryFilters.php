<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDiscoveryFilters
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['search', 'city', 'category', 'platform', 'language', 'minFollowers', 'maxFollowers', 'budget', 'verified', 'available', 'sort', 'page'] as $filter) {
            $value = $request->query($filter);
            abort_if($value !== null && (! is_scalar($value) || mb_strlen((string) $value) > 500), 400, 'Invalid discovery filter.');
        }

        return $next($request);
    }
}
