<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    //
    protected $table = 'Schools';

    protected $primaryKey = 'school_id';

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'school_type',
        'student_number',
        'scholarship_request',
        'internship_request',
        'practice_request',
    ];

    public $incrementing = false;

    // 説明会参加申し込み
    public function fairApplications()
    {
        return $this->hasMany(FairApplication::class, 'school_id', 'school_id');
    }
}
