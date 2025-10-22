<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cotisation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_id', 'user_id', 'montant', 'preuve_path', 'date_versement', 'created_by'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
