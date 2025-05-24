<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GroupController extends Controller
{
      // 🔍 Liste des groupes
      public function index()
      {
          $user = Auth::user();
  
          $created = Group::where('created_by', $user->id)->withCount('participants')->get();
          $joined = Group::whereIn('id', $user->groupParticipations()->pluck('group_id'))->get();
  
          return Inertia::render('Dashboard', [
              'created' => $created,
              'joined' => $joined,
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
          ]);
  
          GroupParticipant::create([
              'group_id' => $group->id,
              'user_id' => Auth::id(),
              'montant_par_defaut' => 0,
              'date_ajout' => now(),
              'statut' => 'actif',
              'is_admin' => true,
          ]);
  
          return redirect()->route('groups.index')->with('success', 'Groupe créé.');
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
