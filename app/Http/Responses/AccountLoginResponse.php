<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountLoginResponse implements LoginResponse
{
    public function toResponse($request): Response
    {
        $request->session()->forget('url.intended');

        return $request->wantsJson() ? response()->json(['two_factor' => false]) : redirect()->route($request->user()->hasVerifiedEmail() ? $request->user()->dashboardRoute() : 'verification.notice');
    }
}
