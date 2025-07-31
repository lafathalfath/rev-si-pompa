<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model
{
    use HasFactory;
    protected $table = 'document';
    protected $guarded = [];

    public function kepemilikan_tanah_poktan(): BelongsToMany {
        return $this->belongsToMany(Poktan::class, 'p_poktan_kepemilikan', 'document_id', 'poktan_id');
    }

    public function bukti_pemanfaatan(): HasMany {
        return $this->hasMany(Pompa::class, 'bukti_id', 'id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
