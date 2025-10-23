<?php

namespace App\Support;

use App\Models\GroupParticipant;
use App\Models\Invitation;
use Illuminate\Http\Request;

trait HandlesPendingInvitation
{
    protected function completePendingInvitation(Request $request): ?int
    {
        $token = $request->session()->pull('pending_invitation_token');

        if (!$token) {
            return null;
        }

        $invitation = Invitation::with('group')->where('token', $token)->first();

        if (!$invitation || $invitation->status === 'accepted') {
            return null;
        }

        $user = $request->user();

        if (!$user || $user->email !== $invitation->email) {
            return null;
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

        return $invitation->group_id;
    }
}
