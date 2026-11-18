<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Models\User;
use App\Notifications\RegistrationConfirmation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'marketing_opt_in' => $validated['marketing_opt_in'] ?? false,
            'communication_preferences' => $validated['communication_preferences'] ?? ['email' => true],
        ]);

        Auth::login($user);
        event(new Registered($user));
        $user->notify(new RegistrationConfirmation);

        return redirect()->route('verification.notice')
            ->with('status', 'Your account has been created. Please verify your email address.');
    }
}
