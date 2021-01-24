<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FairsType extends Model
{
    protected $table = 'FairsTypes';

    protected $fillable = [
        "fair_id",
        "fair_type"
    ];
    const UPDATED_AT = null;
    const CREATED_AT = null;
    const DELETED_AT = null;
}
