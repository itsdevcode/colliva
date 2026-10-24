<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountRegisterResponse implements RegisterResponse
{
    public function toResponse($request): Response
    {
        $request->session()->forget('url.intended');

        return $request->wantsJson() ? response()->json([], 201) : redirect()->route('verification.notice');
    }
}
