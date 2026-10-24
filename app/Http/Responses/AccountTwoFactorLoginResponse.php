<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountTwoFactorLoginResponse implements TwoFactorLoginResponse
{
    public function toResponse($request): Response
    {
        return $request->wantsJson() ? response()->json('', 204) : (new AccountLoginResponse)->toResponse($request);
    }
}
