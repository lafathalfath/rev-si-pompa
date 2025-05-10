<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuasTanam extends Model
{
    use HasFactory;

    protected $table = 'luas_tanam';
    protected $guarded = [];

    public function pompa_diterima(): BelongsTo {
        return $this->belongsTo(PompaDiterima::class, 'pompa_diterima_id', 'id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
