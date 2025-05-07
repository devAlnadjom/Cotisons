<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'group_id', 'email', 'token', 'status'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}

