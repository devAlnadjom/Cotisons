<?php

namespace App\Models;

use App\Policies\GroupPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'created_by', 'periodicity'];
    
    protected $policies = [
        'update' => GroupPolicy::class
    ];

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
