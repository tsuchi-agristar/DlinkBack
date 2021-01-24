<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HospitalScholarship extends Model
{

    protected $table = "HospitalScholarships";

    protected $primaryKey = "append_information_id";

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "append_information_id",
        // IF仕様書とマイグレーションファイルで綴り間違い
        "target_person",
        "document_submitted",
        "selection_system",
        "loan_amount",
        "loan_period_start",
        "loan_period_end",
        "payback_period_start",
        "payback_period_end",
        "payback",
        "payback_exemption",
        "payback_exemption_condition"
    ];
}
