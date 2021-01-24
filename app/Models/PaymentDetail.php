<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{

    //
    protected $table = 'PaymentDetails';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'estimate_id'
    ];

    const UPDATED_AT = null;
    const CREATED_AT = null;
    const DELETED_AT = null;

    // 請求書
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    // 見積
    public function estimate()
    {
        return $this->hasOne(Estimate::class, 'estimate_id', 'estimate_id');
    }
}
