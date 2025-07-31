<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    protected $guarded = [];

    public $timestamps = false;

    public function pj(): BelongsTo {
        return $this->belongsTo(User::class, 'pj_id', 'id');
    }

    public function kabupaten(): BelongsTo {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id', 'id');
    }

    public function desa(): HasMany {
        return $this->hasMany(Desa::class, 'kecamatan_id', 'id');
    }
}
