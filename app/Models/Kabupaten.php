<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';
    protected $guarded = [];

    public function pj(): BelongsTo {
        return $this->belongsTo(User::class, 'pj_id', 'id');
    }

    public function provinsi(): BelongsTo {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
    
    public function kecamatan(): HasMany {
        return $this->hasMany(Kecamatan::class, 'kabupaten_id', 'id');
    }
}
