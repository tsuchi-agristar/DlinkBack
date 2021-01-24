<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationBlock extends Model
{
    protected $table = 'NotificationBlocks';

    protected $primaryKey = 'organization_id';

    protected $keyType = 'string';

    protected $fillable = [
        'organization_id'
    ];

    public $incrementing = false;
}
