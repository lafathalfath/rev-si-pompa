<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poktan extends Model
{
    use HasFactory;

    protected $table = 'poktan';
    protected $guarded = [];

    public function desa(): BelongsTo {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }

    public function kepemilikan_tanah(): BelongsToMany {
        return $this->belongsToMany(Document::class, 'p_poktan_kepemilikan', 'poktan_id', 'document_id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function pompa_usulan(): HasMany {
        return $this->hasMany(PompaUsulan::class, 'poktan_id', 'id');
    }
}
