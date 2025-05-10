<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PompaDimanfaatkan extends Model
{
    use HasFactory;

    protected $table = 'pompa_dimanfaatkan';
    protected $guarded = [];

    public function pompa_diterima(): BelongsTo {
        return $this->belongsTo(PompaDiterima::class, 'pompa_diterima_id', 'id');
    }

    public function bukti_pembelian_dimanfaatkan(): BelongsToMany {
        return $this->belongsToMany(Document::class, 'p_dimanfaatkan_bukti', 'pompa_dimanfaatkan_id', 'document_id');
    }

    public function create_by(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function update_by(): BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
