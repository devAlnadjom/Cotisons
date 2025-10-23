<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'group_participant_id',
        'montant',
        'date_paiement',
        'motif',
        'created_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participant()
    {
        return $this->belongsTo(GroupParticipant::class, 'group_participant_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
