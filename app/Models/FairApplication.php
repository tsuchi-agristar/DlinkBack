<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class FairApplication extends Model
{
    use SoftDeletes;

    //
    protected $table = 'FairApplications';

    protected $primaryKey = 'application_id';

    protected $keyType = 'string';

    protected $fillable = [
        'application_id',
        'fair_id',
        'school_id',
        'application_datetime',
        'application_status',
        'estimate_participant_number',
        'format',
        'comment'
    ];

    public $incrementing = false;

    // 学校
    public function school()
    {
        return $this->hasOne(School::class, 'school_id', 'school_id');
    }

    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'school_id');
    }

    // 説明会
    public function fair()
    {
        return $this->hasOne(Fair::class, 'fair_id', 'fair_id');
    }

    // 説明会
    public function fair_withtrashed()
    {
        return $this->hasOne(Fair::class, 'fair_id', 'fair_id')->withTrashed();
    }

    // 説明会種別
    public function fairs_types()
    {
        return $this->hasMany(FairsType::class, 'fair_id', 'fair_id');
    }

    // オンラインイベント
    public function online_events()
    {
        return $this->hasMany(OnlineEvent::class, 'fair_id', 'fair_id');
    }

    // --
    // 説明会申込検索
    public function scopeSearch($query, Request $request)
    {
        return $query
            ->searchSchoolID($request->school_id)
            ->searchApplicationStatus($request->application_status);
    }

    // 説明会検索（学校）
    public function scopeSearchSchoolID($query, ?string $school_id)
    {
        if (! is_null($school_id)) {
            return $query->where('FairApplications.school_id', $school_id);
        }
        return $query;
    }
    // 申込状態検索
    public function scopeSearchApplicationStatus($query, ?string $application_status)
    {
        if (! is_null($application_status)) {
            return $query->whereIn('FairApplications.application_status', explode(",", $application_status));
        }
        return $query;
    }
}
