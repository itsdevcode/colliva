<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountVerifyEmailResponse implements VerifyEmailResponse
{
    public function toResponse($request): Response
    {
        $request->session()->forget('url.intended');

        return $request->wantsJson() ? response()->json('', 204) : redirect(route('dashboard', absolute: false).'?verified=1');
    }
}
