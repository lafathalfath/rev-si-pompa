<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    use HasFactory;

    protected $table = 'provinsi';
    protected $guarded = [];

    public $timestamps = false;

    public function pj(): BelongsTo {
        return $this->belongsTo(User::class, 'pj_id', 'id');
    }

    public function kabupaten(): HasMany {
        return $this->hasMany(Kabupaten::class, 'provinsi_id', 'id');
    }
}
