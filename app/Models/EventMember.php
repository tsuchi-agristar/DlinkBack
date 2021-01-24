<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    //
    protected $table = 'EventMembers';

    protected $primaryKey = [
        'event_id',
        'organization_id'
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        "event_id",
        "organization_id",
        "member_role",
    ];

    const UPDATED_AT = null;
    const CREATED_AT = null;
    const DELETED_AT = null;


    // オンラインイベント
    public function onlineEvent()
    {
        return $this->hasOne(OnlineEvent::class, 'event_id', 'event_id');
    }
    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'organization_id');
    }
}
