<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';
    protected $guarded = [];

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver(): BelongsTo {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function links(): HasMany {
        return $this->hasMany(NotificationLink::class, 'notification_id', 'id');
    }
}
