<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class GroupController extends Controller
{
      // 🔍 Liste des groupes
      public function index()
      {
          /** @var User $user */
          $user = Auth::user();

          // Groups created by user with participants count and last cotisation date
          $created = Group::where('created_by', $user->id)
              ->withCount('participants')
              ->with(['cotisations' => fn($q) => $q->latest('date_versement')->limit(1)])
              ->orderByDesc('created_at')
              ->take(6)
              ->get()
              ->map(fn($g) => [
                  'id' => $g->id,
                  'name' => $g->name,
                  'description' => $g->description,
                  'periodicity' => $g->periodicity,
                  'participants_count' => $g->participants_count,
                  'last_cotisation_at' => optional($g->cotisations->first())->date_versement,
                  'balance' => $g->balance,
              ]);

          // Groups the user joined (via groupParticipations)
          $joined = Group::whereIn('id', $user->groupParticipations()->pluck('group_id'))
              ->withCount('participants')
              ->orderByDesc('created_at')
              ->take(6)
              ->get();

          // Summary counters
          $summary = [
              'groups_count' => $user->groupsCreated()->count(),
              'participants_count' => $user->groupParticipations()->count(),
              'pending_invitations' => \App\Models\Invitation::where('email', $user->email)->where('status', 'pending')->count(),
              'unpaid_cotisations_count' => \App\Models\Cotisation::where('user_id', $user->id)->whereNull('date_versement')->count(),
          ];

          // recent cotisations (user is author or participant)
          $recent_cotisations = \App\Models\Cotisation::with(['group:id,name', 'author:id,name', 'participant:id,name'])
              ->where(function($q) use ($user) {
                  $q->where('created_by', $user->id)
                    ->orWhere('user_id', $user->id);
              })
              ->orderByDesc('date_versement')
              ->take(8)
              ->get()
              ->map(fn($c) => [
                  'id' => $c->id,
                  'amount' => $c->montant,
                  'date_versement' => $c->date_versement,
                  'group' => $c->group ? ['id' => $c->group->id, 'name' => $c->group->name] : null,
                  'author' => $c->author ? ['id' => $c->author->id, 'name' => $c->author->name] : null,
                  'preuve_url' => $c->preuve_path ? asset('storage/' . $c->preuve_path) : null,
              ]);

          return Inertia::render('Dashboard', [
              'created' => $created,
              'joined' => $joined,
              'summary' => $summary,
              'recent_cotisations' => $recent_cotisations,
          ]);
      }
  
      public function create()
      {
          return Inertia::render('Groups/Create');
      }
  
      public function store(Request $request)
      {
          $request->validate([
              'name' => 'required|string|max:255',
              'description' => 'nullable|string',
              'periodicity' => 'required|string|in:monthly,bi-weekly,weekly,custom',
          ]);
  
          $group = Group::create([
              'name' => $request->name,
              'description' => $request->description,
              'periodicity' => $request->periodicity,
              'created_by' => Auth::id(),
              'balance' => 0,
          ]);
  
          GroupParticipant::create([
              'group_id' => $group->id,
              'user_id' => Auth::id(),
              'montant_par_defaut' => 0,
              'date_ajout' => now(),
              'statut' => 'actif',
              'is_admin' => true,
          ]);

          // Use 303 PRG so Inertia can perform a client-side visit to the new group's page
          return redirect()->route('groups.show', $group->id)->with('success', 'Groupe créé.')->setStatusCode(303);
      }

      public function list(Request $request)
      {
          $user = $request->user();

          $created = Group::withCount('participants')
              ->with(['cotisations' => fn($q) => $q->latest('date_versement')->limit(1)])
              ->where('created_by', $user->id)
              ->orderBy('name')
              ->get()
              ->map(fn($group) => [
                  'id' => $group->id,
                  'name' => $group->name,
                  'description' => $group->description,
                  'periodicity' => $group->periodicity,
                  'participants_count' => $group->participants_count,
                  'last_cotisation_at' => optional($group->cotisations->first())->date_versement,
                  'balance' => $group->balance,
              ]);

          $joined = Group::withCount('participants')
              ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
              ->where('created_by', '!=', $user->id)
              ->orderBy('name')
              ->get()
              ->map(fn($group) => [
                  'id' => $group->id,
                  'name' => $group->name,
                  'description' => $group->description,
                  'periodicity' => $group->periodicity,
                  'participants_count' => $group->participants_count,
                  'balance' => $group->balance,
              ]);

          return Inertia::render('Groups/List', [
              'created' => $created,
              'joined' => $joined,
          ]);
      }

      // Affiche la page de détail d'un groupe
      public function show(Group $group)
      {
          $this->authorize('view', $group);

          $group->load([
              'creator:id,name',
              'participants.user:id,name',
              'cotisations' => function($q) {
                  $q->with('participant:id,name')->orderByDesc('date_versement')->take(20);
              },
              'payments' => function($q) {
                  $q->with(['recipient:id,name', 'author:id,name'])->orderByDesc('date_paiement')->orderByDesc('created_at')->take(20);
              },
          ]);

          $authUser = Auth::user();

          $participants = $group->participants->map(fn($p) => [
              'id' => $p->id,
              'user_id' => $p->user_id,
              'user' => $p->user ? ['id' => $p->user->id, 'name' => $p->user->name] : null,
              'montant_par_defaut' => $p->montant_par_defaut,
              'statut' => $p->statut,
              'is_admin' => (bool) $p->is_admin,
          ])->values();

          $activeParticipants = $group->participants
              ->filter(fn($p) => $p->statut === 'actif')
              ->map(fn($p) => [
                  'id' => $p->id,
                  'user_id' => $p->user_id,
                  'user' => $p->user ? ['id' => $p->user->id, 'name' => $p->user->name] : null,
                  'statut' => $p->statut,
              ])
              ->values();

          $userIsAdmin = false;
          if ($authUser) {
              $userIsAdmin = $group->created_by === $authUser->id
                  || $group->participants->contains(fn($p) => $p->user_id === $authUser->id && (bool) $p->is_admin);
          }

          return Inertia::render('Groups/Show', [
              'group' => [
                  'id' => $group->id,
                  'name' => $group->name,
                  'description' => $group->description,
                  'periodicity' => $group->periodicity,
                  'creator' => $group->creator ? ['id' => $group->creator->id, 'name' => $group->creator->name] : null,
                  'balance' => $group->balance,
                  'participants' => $participants,
                  'activeParticipants' => $activeParticipants,
                  'payments' => $group->payments->map(fn($payment) => [
                      'id' => $payment->id,
                      'montant' => $payment->montant,
                      'date_paiement' => $payment->date_paiement,
                      'motif' => $payment->motif,
                      'recipient' => $payment->recipient ? ['id' => $payment->recipient->id, 'name' => $payment->recipient->name] : null,
                      'participant_id' => $payment->group_participant_id,
                      'author' => $payment->author ? ['id' => $payment->author->id, 'name' => $payment->author->name] : null,
                  ])->values(),
                  'cotisations' => $group->cotisations->map(fn($c) => [
                      'id' => $c->id,
                      'montant' => $c->montant,
                      'date_versement' => $c->date_versement,
                      // expose participant under `user` key for frontend compatibility
                      'user' => $c->participant ? ['id' => $c->participant->id, 'name' => $c->participant->name] : null,
                      'participant_id' => $c->user_id,
                  ])->values(),
                  'permissions' => [
                      'can_add_cotisation' => $userIsAdmin,
                      'can_invite' => $userIsAdmin,
                  ],
              ],
          ]);
      }

    // Store a new cotisation for a group
    public function storeCotisation(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        $user = $request->user();

        $isAdmin = $user && (
            $group->created_by === $user->id ||
            GroupParticipant::where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->where('is_admin', true)
                ->exists()
        );

        abort_unless($isAdmin, 403);

        $validated = $request->validate([
            'participant_id' => [
                'required',
                'integer',
                Rule::exists('group_participants', 'id')
                    ->where(fn($query) => $query->where('group_id', $group->id)->where('statut', 'actif')),
            ],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'date_versement' => ['required', 'date'],
        ]);

        $participant = GroupParticipant::with('user')
            ->where('group_id', $group->id)
            ->findOrFail($validated['participant_id']);

        DB::transaction(function () use ($group, $participant, $validated, $user) {
            Cotisation::create([
                'group_id' => $group->id,
                'user_id' => $participant->user_id,
                'montant' => $validated['montant'],
                'date_versement' => $validated['date_versement'],
                'created_by' => $user->id,
            ]);

            $group->increment('balance', $validated['montant']);
        });

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Cotisation enregistrée avec succès.')
            ->setStatusCode(303);
    }

    public function storePayment(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        $user = $request->user();

        $isAdmin = $user && (
            $group->created_by === $user->id ||
            GroupParticipant::where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->where('is_admin', true)
                ->exists()
        );

        abort_unless($isAdmin, 403);

        $validated = $request->validate([
            'participant_id' => [
                'required',
                'integer',
                Rule::exists('group_participants', 'id')
                    ->where(fn($query) => $query->where('group_id', $group->id)->where('statut', 'actif')),
            ],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'date_paiement' => ['required', 'date'],
            'motif' => ['nullable', 'string', 'max:255'],
        ]);

        if (floatval($validated['montant']) > floatval($group->balance)) {
            throw ValidationException::withMessages([
                'montant' => 'Le montant dépasse le solde du groupe.',
            ]);
        }

        $participant = GroupParticipant::with('user')
            ->where('group_id', $group->id)
            ->findOrFail($validated['participant_id']);

        DB::transaction(function () use ($group, $participant, $validated, $user) {
            Payment::create([
                'group_id' => $group->id,
                'user_id' => $participant->user_id,
                'group_participant_id' => $participant->id,
                'montant' => $validated['montant'],
                'date_paiement' => $validated['date_paiement'],
                'motif' => $validated['motif'] ?? null,
                'created_by' => $user->id,
            ]);

            $group->decrement('balance', $validated['montant']);
        });

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Paiement enregistré et solde mis à jour.')
            ->setStatusCode(303);
    }
  
      // 🛠️ Formulaire de modification (si utilisé)
      public function edit(Group $group)
      {
          $this->authorize('update', $group);
  
          return Inertia::render('Groups/Edit', [
              'group' => $group
          ]);
      }
  
      // 🔁 Mise à jour
      public function update(Request $request, Group $group)
      {
          $this->authorize('update', $group);
  
          $request->validate([
              'name' => 'required|string|max:255',
              'description' => 'nullable|string',
              'periodicity' => 'required|string|in:monthly,weekly,custom',
          ]);
  
          $group->update($request->only('name', 'description', 'periodicity'));
  
          return redirect()->route('groups.index')->with('success', 'Groupe mis à jour.');
      }
  
      // 🗑️ Suppression (soft delete)
      public function destroy(Group $group)
      {
          $this->authorize('delete', $group);
  
          $group->delete();
  
          return redirect()->route('groups.index')->with('success', 'Groupe supprimé.');
      }
}
