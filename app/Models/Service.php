<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'Services';

    protected $primaryKey = 'service_id';

    protected $keyType = 'string';

    protected $fillable = [
        'service_id',
        'service_type',
        'fair_format',
        'school_number',
        'location',
        'price'
    ];

    public $incrementing = false;
}
