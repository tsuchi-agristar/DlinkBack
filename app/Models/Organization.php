<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Organization extends Model
{

    use SoftDeletes;

    //
    protected $table = 'Organizations';

    protected $primaryKey = 'organization_id';

    protected $keyType = 'string';

    protected $fillable = [
        'organization_id',
        'organization_type',
        'organization_name',
        'organization_name_kana',
        'prefecture',
        'city',
        'address',
        'homepage',
        'dummy'
    ];

    public $incrementing = false;

    protected $guarded = [];

    // --
    public function hospital()
    {
        return $this->hasOne('App\Models\Hospital', 'hospital_id', 'organization_id');
    }

    public function user()
    {
        // 現状、hasOneでOK
        return $this->hasOne('App\Models\User', 'organization_id', 'organization_id');
    }

    // 学校
    public function school()
    {
        return $this->hasOne(School::class, 'school_id', 'organization_id');
    }

    // 学校
    public function schools()
    {
        return $this->hasMany(School::class, 'school_id', 'organization_id');
    }

    // ユーザー
    public function users()
    {
        return $this->hasMany(User::class, 'organization_id', 'organization_id');
    }

    // イベントメンバー
    public function eventMembers()
    {
        return $this->hasMany(EventMember::class, 'organization_id', 'organization_id');
    }

    // --
    // 組織(学校)取得
    public function scopeTypeSchool($query)
    {
        return $query->searchOrganizationType(config('const.ORGANIZATION_TYPE')['SCHOOL']);
    }
  // 組織(学校)取得
  public function scopeTypeHospital($query)
  {
      return $query->searchOrganizationType(config('const.ORGANIZATION_TYPE')['HOSPITAL']);
  }
    // 組織検索
    public function scopeSearch($query, Request $request)
    {
        return $query->searchOrganizationType($request->organization_type);
    }

    // 組織検索（組織タイプ）
    public function scopeSearchOrganizationType($query, ?string $organization_type)
    {
        if (! is_null($organization_type)) {
            return $query->where('Organizations.organization_type', $organization_type);
        }
        return $query;
    }

    // 説明会
    public function fair()
    {
        return $this->hasMany(Fair::class, "hospital_id", "organization_id");
    }
}
