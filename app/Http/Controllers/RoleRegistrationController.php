<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

class RoleRegistrationController extends Controller
{
    public function create(string $role): View
    {
        return view('livewire.auth.register', ['role' => $role, 'locations' => Location::orderBy('name')->get(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request, string $role, CreatesNewUsers $creator): RegisterResponse
    {
        $request->validate(['role' => ['prohibited']]);
        $request->merge(['role' => $role]);

        return app(RegisteredUserController::class)->store($request, $creator);
    }
}
