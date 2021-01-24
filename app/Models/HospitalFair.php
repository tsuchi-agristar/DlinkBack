<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HospitalFair extends Model
{

    protected $table = "HospitalFairs";

    protected $primaryKey = "append_information_id";

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "append_information_id",
        "target_person",
    ];

    // 病院説明会種別
    public function hospital_fair_type()
    {
        return $this->hasMany(HospitalFairType::class, 'append_information_id', 'append_information_id');
    }
}
