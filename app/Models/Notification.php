<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'Notifications';

    protected $primaryKey = 'notification_id';

    protected $keyType = 'string';

    protected $fillable = [
        'notification_id',
        'notification_type',
        'notification_at',
        'title',
        'content_school',
        'content_hospital',
    ];

    public $incrementing = false;
}
