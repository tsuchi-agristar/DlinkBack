<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NotificationQueue extends Model
{
    protected $table = 'NotificationQueues';

    protected $primaryKey = 'notification_id';

    protected $keyType = 'string';

    protected $fillable = [
        'notification_id',
        'notification_type',
        'operation_id',
        'notification_at'
    ];

    public $incrementing = false;

    public $timestamps = false;
}
