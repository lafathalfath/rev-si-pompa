<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pompa extends Model
{
    use HasFactory;

    protected $table = 'pompa';
    protected $guarded = [];

    public function pemanfaatan(): HasMany {
        return $this->hasMany(PemanfaatanPompa::class, 'pompa_id', 'id');
    }

    public function desa(): BelongsTo {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }

    public function poktan(): BelongsTo {
        return $this->belongsTo(Poktan::class, 'poktan_id', 'id');
    }

    public function status(): BelongsTo {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    
}
