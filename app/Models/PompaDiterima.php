<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PompaDiterima extends Model
{
    use HasFactory;

    protected $table = 'pompa_diterima';
    protected $guarded = [];

    public function pompa_usulan(): BelongsTo {
        return $this->belongsTo(PompaUsulan::class, 'pompa_usulan_id', 'id');
    }

    public function pompa_dimanfaatkan(): HasMany {
        return $this->hasMany(PompaDimanfaatkan::class, 'pompa_diterima_id', 'id');
    }

    public function luas_tanam(): HasMany {
        return $this->hasMany(LuasTanam::class, 'pompa_diterima_id', 'id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
