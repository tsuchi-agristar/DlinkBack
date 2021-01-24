<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Fair extends Model
{
    use SoftDeletes;

    protected $table = 'Fairs';

    protected $primaryKey = 'fair_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        "fair_id",
        "hospital_id",
        "fair_status",
        "plan_start_at",
        "plan_end_at",
    ];

    // オンラインイベント
    public function online_events()
    {
        return $this->hasMany(OnlineEvent::class, "fair_id", "fair_id");
    }
    // 説明会種別
    public function fair_type()
    {
        return $this->hasMany(FairsType::class, 'fair_id', 'fair_id');
    }

    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'hospital_id');
    }

    // オンラインイベント（新着順）
    public function online_events_latest()
    {
        return $this->hasMany(OnlineEvent::class, 'fair_id', 'fair_id')->latest();
    }

    // 説明会参加申し込み
    public function fair_applications()
    {
        return $this->hasMany(FairApplication::class, 'fair_id', 'fair_id');
    }
}
