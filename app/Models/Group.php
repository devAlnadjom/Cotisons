<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'created_by', 'periodicity'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(GroupParticipant::class);
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
