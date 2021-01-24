<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FairDetail extends Model
{
    //

    protected $table = 'FairDetails';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        "fair_id",
        "append_information_id",
    ];

    // 説明会
    public function fair()
    {
        return $this->belongsTo(Fair::class, "fair_id", "fair_id");
    }

    // 付属情報 (appendメソッドが親クラスで定義済みのため)
    public function append_info()
    {
        return $this->hasOne(HospitalAppend::class, "append_information_id", "append_information_id");
    }
    
}
