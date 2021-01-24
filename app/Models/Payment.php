<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    //
    protected $table = 'Payments';

    protected $primaryKey = 'payment_id';

    protected $keyType = 'string';

    protected $fillable = [
        'payment_id',
        'payment_hospital_id',
        'payment_month',
        'payment_status',
        'payment_price'
    ];

    public $incrementing = false;

    // 組織
    public function organization()
    {
        return $this->hasOne(Organization::class, 'organization_id', 'payment_hospital_id');
    }

    // 請求書詳細
    public function payment_details()
    {
        return $this->hasMany(PaymentDetail::class, 'payment_id', 'payment_id');
    }
}
