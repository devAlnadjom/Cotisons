<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Invitation;
use App\Support\HandlesPendingInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    use HandlesPendingInvitation;

    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        $prefillEmail = $request->session()->get('login_prefill_email');

        if (!$prefillEmail) {
            $prefillEmail = $request->query('email');
        }

        if (!$prefillEmail && $request->has('invitation_token')) {
            $token = $request->query('invitation_token');
            $invitationEmail = Invitation::where('token', $token)->value('email');

            if ($invitationEmail) {
                $prefillEmail = $invitationEmail;
                $request->session()->put('pending_invitation_token', $token);
                $request->session()->flash('login_prefill_email', $invitationEmail);
            }
        }

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'prefillEmail' => $prefillEmail,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}
