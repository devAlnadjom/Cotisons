<?php

namespace App\Http\Controllers;

use App\Models\GroupParticipant;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
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

        return response()->json(['message' => 'Invitation envoyée.']);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status === 'accepted') {
            return redirect('/login')->with('message', 'Invitation déjà acceptée.');
        }

        // Vérifie si l'utilisateur existe
        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            // Redirige vers page d’inscription personnalisée
            return redirect("/register?email={$invitation->email}&token={$token}");
        }

        // Ajoute automatiquement le participant au groupe
        GroupParticipant::create([
            'group_id' => $invitation->group_id,
            'user_id' => $user->id,
            'montant_par_defaut' => 0,
            'date_ajout' => now(),
            'statut' => 'actif',
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect('/dashboard')->with('message', 'Vous avez rejoint le groupe.');
    }
}
