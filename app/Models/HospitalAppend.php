<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HospitalAppend extends Model
{
    use SoftDeletes;

    protected $table = "HospitalAppends";

    protected $primaryKey = "append_information_id";

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "append_information_id",
        "append_information_type",
        "hospital_id",
        "recruiting_job_type",
        "recruiting_period_start",
        "recruiting_period_end",
        "content",
        "various_matters",
        "other"
    ];
    // インターシップ情報
    public function hospital_intership()
    {
        return $this->hasOne(HospitalIntership::class, "append_information_id", "append_information_id");
    }

    // 実習情報
    public function hospital_practice()
    {
        return $this->hasOne(HospitalPractice::class, "append_information_id", "append_information_id");
    }

    // スカラシップ情報
    public function hospital_scholarship()
    {
        return $this->hasOne(HospitalScholarship::class, "append_information_id", "append_information_id");
    }

    // 病院説明会情報
    public function hospital_fair()
    {
        return $this->hasOne(HospitalFair::class, "append_information_id", "append_information_id")->with("hospital_fair_type");
    }

    // 所属する病院情報
    public function hospital()
    {
        return $this->belongsTo(Hospital::class , "hospital_id", "hospital_id");
    }

}
