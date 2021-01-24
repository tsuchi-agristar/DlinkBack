<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionaryHospital extends Model
{

    //
    protected $table = 'QuestionaryHospitals';

    protected $primaryKey = null;

    protected $fillable = [
        'questionary_id',
        'hospital_id'
    ];

    public $incrementing = false;
    public $timestamps = false;

    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'hospital_id');
    }

    // 説明会アンケート
    public function questionary()
    {
        return $this->belongsTo(Questionary::class, 'questionary_id', 'questionary_id');
    }
}
