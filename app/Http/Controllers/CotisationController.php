<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CotisationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $cotisations = Cotisation::with(['group:id,name', 'author:id,name'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('date_versement')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->through(function ($cotisation) {
                return [
                    'id' => $cotisation->id,
                    'montant' => $cotisation->montant,
                    'date_versement' => $cotisation->date_versement,
                    'created_at' => $cotisation->created_at,
                    'group' => $cotisation->group ? [
                        'id' => $cotisation->group->id,
                        'name' => $cotisation->group->name,
                    ] : null,
                    'author' => $cotisation->author ? [
                        'id' => $cotisation->author->id,
                        'name' => $cotisation->author->name,
                    ] : null,
                ];
            });

        $metrics = [
            'total' => Cotisation::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            })->count(),
            'total_amount' => Cotisation::where('user_id', $user->id)->sum('montant'),
            'pending' => Cotisation::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            })->whereNull('date_versement')->count(),
        ];

        return Inertia::render('cotisations/Index', [
            'cotisations' => $cotisations,
            'metrics' => $metrics,
        ]);
    }
}
