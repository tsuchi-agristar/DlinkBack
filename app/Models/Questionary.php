<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionary extends Model
{
    use SoftDeletes;

    //
    protected $table = 'Questionary';

    protected $primaryKey = 'questionary_id';

    protected $keyType = 'string';

    protected $fillable = [
        'questionary_id',
        'school_id',
        'answered_datetime',
        'desire_start_at',
        'desire_end_at',
        'comment',
    ];

    public $incrementing = false;

    // 希望説明会種別
    public function questionary_fair_types()
    {
        return $this->hasMany(QuestionaryFairType::class, 'questionary_id', 'questionary_id');
    }

    // 希望病院
    public function questionary_hospitals()
    {
        return $this->hasMany(QuestionaryHospital::class, 'questionary_id', 'questionary_id');
    }

    // 希望地域
    public function questionary_places()
    {
        return $this->hasMany(QuestionaryPlace::class, 'questionary_id', 'questionary_id');
    }

    // 希望病院種別
    public function questionary_hospital_types()
    {
        return $this->hasMany(QuestionaryHospitalType::class, 'questionary_id', 'questionary_id');
    }

    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'school_id');
    }
}
