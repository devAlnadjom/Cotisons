<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $payments = Payment::with(['group:id,name', 'recipient:id,name', 'author:id,name'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('date_paiement')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->through(function (Payment $payment) {
                return [
                    'id' => $payment->id,
                    'montant' => $payment->montant,
                    'date_paiement' => $payment->date_paiement,
                    'motif' => $payment->motif,
                    'group' => $payment->group ? [
                        'id' => $payment->group->id,
                        'name' => $payment->group->name,
                    ] : null,
                    'recipient' => $payment->recipient ? [
                        'id' => $payment->recipient->id,
                        'name' => $payment->recipient->name,
                    ] : null,
                    'author' => $payment->author ? [
                        'id' => $payment->author->id,
                        'name' => $payment->author->name,
                    ] : null,
                ];
            });

        $metrics = [
            'total' => Payment::where('created_by', $user->id)->count(),
            'total_amount_sent' => Payment::where('created_by', $user->id)->sum('montant'),
            'total_amount_received' => Payment::where('user_id', $user->id)->sum('montant'),
        ];

        return Inertia::render('payments/Index', [
            'payments' => $payments,
            'metrics' => $metrics,
        ]);
    }
}
