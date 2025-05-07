<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupParticipant extends Model
{
    protected $fillable = [
        'group_id', 'user_id', 'montant_par_defaut', 'date_ajout', 'statut', 'is_admin'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
