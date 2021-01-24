<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
    use SoftDeletes;

    protected $table = 'Estimates';

    protected $primaryKey = 'estimate_id';

    protected $keyType = 'string';

    protected $fillable = [
        'estimate_id',
        'event_id',
        'estimate_status',
        'regular_price',
        'discount_price',
        'estimate_price'
    ];

    public $incrementing = false;

    // オンラインイベント
    public function online_event()
    {
        return $this->hasOne(OnlineEvent::class, 'event_id', 'event_id');
    }
}
