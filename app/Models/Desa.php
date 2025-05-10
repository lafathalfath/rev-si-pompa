<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    use HasFactory;

    protected $table = 'desa';
    protected $guarded = [];

    public function pj(): BelongsTo {
        return $this->belongsTo(User::class, 'pj_id', 'id');
    }

    public function kecamatan(): BelongsTo {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

    public function poktan(): HasMany {
        return $this->hasMany(Poktan::class, 'desa_id', 'id');
    }

    public function pompa_usulan(): HasMany {
        return $this->hasMany(PompaUsulan::class, 'desa_id', 'id');
    }
}
