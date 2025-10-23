<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\HandlesPendingInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    use HandlesPendingInvitation;

    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        $prefillEmail = $request->session()->get('login_prefill_email');

        if (!$prefillEmail) {
            $prefillEmail = $request->query('email');
        }

        return Inertia::render('auth/Register', [
            'prefillEmail' => $prefillEmail,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $joinedGroupId = $this->completePendingInvitation($request);
        $redirectUrl = $request->session()->pull('after_login_redirect');

        if ($joinedGroupId) {
            $request->session()->flash('success', 'Invitation acceptée. Bienvenue dans le groupe !');

            if ($redirectUrl) {
                return redirect()->intended($redirectUrl);
            }

            return redirect()->intended(route('groups.show', ['group' => $joinedGroupId], absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
