<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ChangePasswordRequest;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Models\User;
use App\Services\Access\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function edit(): View
    {
        return view('customer.profile.edit', ['user' => Auth::user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $originalEmail = $user->email;

        $user->update($request->validated());

        if ($originalEmail !== $user->email) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
        }

        $this->audit->record('customer.profile_updated', $user, User::class, $user->id);

        return back()->with('status', 'Your profile has been updated.');
    }

    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update(['password' => $request->validated('password')]);

        $this->audit->record('customer.password_changed', $user, User::class, $user->id);

        return back()->with('status', 'Your password has been changed.');
    }
}
