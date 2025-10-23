<?php

namespace App\Http\Controllers;

use App\Models\GroupParticipant;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class GroupInvitationController extends Controller
{
    //
    public function invite(Request $request, $groupId)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $groupId = (int) $groupId;

        // Vérifie si une invitation existe déjà
        $invitation = Invitation::where('group_id', $groupId)
            ->where('email', $email)
            ->first();

        if (!$invitation) {
            $invitation = Invitation::create([
                'group_id' => $groupId,
                'email' => $email,
                'token' => Str::uuid(),
                'status' => 'pending'
            ]);
        }

        // Envoie l'email
        Mail::to($email)->send(new \App\Mail\GroupInvitationMail($invitation));

        return redirect()
            ->route('groups.show', $groupId)
            ->with('success', 'Invitation envoyée.');
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::with('group')->where('token', $token)->firstOrFail();

        if ($invitation->status === 'accepted') {
            $request->session()->flash('login_prefill_email', $invitation->email);

            return redirect()
                ->route('login')
                ->with('warning', 'Invitation déjà acceptée.');
        }

        // Vérifie si l'utilisateur existe déjà
        $userExists = User::where('email', $invitation->email)->exists();

        if (!Auth::check()) {
            $request->session()->flash('login_prefill_email', $invitation->email);
            $request->session()->put('pending_invitation_token', $token);
            $request->session()->put('after_login_redirect', route('groups.show', $invitation->group_id, absolute: false));

            if (!$userExists) {
                return redirect("/register?email={$invitation->email}&token={$token}");
            }

            return redirect()
                ->route('login')
                ->with('success', 'Connectez-vous pour rejoindre le groupe.');
        }

        $user = $request->user();

        if ($user->email !== $invitation->email) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Cette invitation ne correspond pas à votre compte.');
        }

        GroupParticipant::firstOrCreate(
            [
                'group_id' => $invitation->group_id,
                'user_id' => $user->id,
            ],
            [
                'montant_par_defaut' => 0,
                'date_ajout' => now()->toDateString(),
                'statut' => 'actif',
                'is_admin' => false,
            ]
        );

        $invitation->update(['status' => 'accepted']);

        return redirect()
            ->route('groups.show', $invitation->group_id)
            ->with('success', 'Vous avez rejoint le groupe.');
    }
}
