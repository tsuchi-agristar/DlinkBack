<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HospitalIntership extends Model
{

    protected $table = "HospitalIntership";

    protected $primaryKey = "append_information_id";

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "append_information_id",
        "target_person",
        "training_period_start",
        "training_period_end"
    ];
}
