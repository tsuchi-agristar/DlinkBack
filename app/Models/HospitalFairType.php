<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalFairType extends Model
{
    protected $table = 'HospitalFairTypes';

    protected $fillable = [
        "append_information_id",
        "hospital_fair_type"
    ];
    const UPDATED_AT = null;
    const CREATED_AT = null;
    const DELETED_AT = null;

    // 所属する病院説明会情報
    public function hospital_fair()
    {
        return $this->belongsTo(HospitalFair::class , "append_information_id", "append_information_id");
    }
}
