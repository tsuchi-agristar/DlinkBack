<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineEvent extends Model
{
    use SoftDeletes;

    //
    protected $table = 'OnlineEvents';

    protected $primaryKey = 'event_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        "event_id",
        "fair_id",
        "event_type",
        "event_status",
        "channel_status",
        "start_at",
        "end_at"
    ];

    // 説明会
    public function fair()
    {
        return $this->belongsTo(Fair::class, 'fair_id', 'fair_id');
    }
    // 組織
    public function event_member()
    {
        return $this->hasMany(EventMember::class, 'event_id', 'event_id');
    }
    // 見積もりレコードのリレーション
    public function estimate()
    {
        return $this->hasOne('App\Models\Estimate', 'event_id', 'event_id');
    }

}
