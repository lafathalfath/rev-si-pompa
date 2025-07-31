<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLink extends Model
{
    use HasFactory;

    protected $table = 'notification_link';
    protected $guarded = [];

    public $timestamps = false;

    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }

}
