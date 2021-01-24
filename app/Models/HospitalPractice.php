<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HospitalPractice extends Model
{

    protected $table = "HospitalPractices";

    protected $primaryKey = "append_information_id";

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "append_information_id",
        "target_person",
        "practice_period_start",
        "practice_period_end"
    ];
}
