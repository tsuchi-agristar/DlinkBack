<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    //
    protected $table = 'Users';

    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'organization_id',
        'mail_address',
        'account_name',
        'password'
    ];

    public $incrementing = false;

    public function organization()
    {
        return $this->hasOne('App\Models\Organization', 'organization_id', 'organization_id');
    }

    //
    // アカウント検索(ログイン)
    public function scopeAccount($query, ?string $account, ?string $password)
    {
        return $query->where('Users.account_name', $account)->where('Users.password', $password);
    }
}
