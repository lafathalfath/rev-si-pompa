<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PompaUsulan extends Model
{
    use HasFactory;

    protected $table = 'pompa_usulan';
    protected $guarded = [];

    public function desa(): BelongsTo {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }

    public function poktan(): BelongsTo {
        return $this->belongsTo(Poktan::class, 'poktan_id', 'id');
    }

    public function pompa_diterima(): HasMany {
        return $this->hasMany(PompaDiterima::class, 'pompa_usulan_id', 'id');
    }
}
