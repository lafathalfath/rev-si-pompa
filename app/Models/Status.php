<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $guarded = [];
    public $timestamps = false;

    public function pompa(): HasMany {
        return $this->hasMany(Pompa::class, 'status_id', 'id');
    }
    
}
