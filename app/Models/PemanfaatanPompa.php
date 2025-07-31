<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemanfaatanPompa extends Model
{
    use HasFactory;

    protected $table = 'pemanfaatan_pompa';
    protected $guarded = [];

    public function pompa(): BelongsTo {
        return $this->belongsTo(Pompa::class, 'pompa_id', 'id');
    }

    public function bukti(): BelongsTo {
        return $this->belongsTo(Document::class, 'bukti_id', 'id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

}
