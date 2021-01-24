<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NotificationDestination extends Model
{
    use SoftDeletes;

    protected $table = 'NotificationDestinations';

    protected $primaryKey = ['notification_id', 'organization_id'];

    protected $keyType = 'string';

    protected $fillable = [
        'notification_id',
        'organization_id',
        'confirm_status'
    ];

    public $incrementing = false;

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    public function scopeNotificationList($query, $organization_id)
	{
        return $query
            ->select(
                DB::raw('notifications.notification_id, organizations.organization_id, notification_type, notification_at, title'),
                DB::raw('(CASE WHEN organization_type = ' . config('const.ORGANIZATION_TYPE.SCHOOL') . ' THEN content_school ELSE content_hospital END) AS content'),
                DB::raw('confirm_status, notifications.created_at, notifications.updated_at, notifications.deleted_at')
            )
            ->leftJoin('notifications', 'notificationdestinations.notification_id', '=', 'notifications.notification_id')
            ->leftJoin('organizations', 'notificationdestinations.organization_id', '=', 'organizations.organization_id')
            ->where('notificationdestinations.organization_id', '=', $organization_id)
            ->orderBy('notificationdestinations.created_at', 'desc');
 	}
}
