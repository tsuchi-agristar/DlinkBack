<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    // テーブル名
    protected $table = 'hospitals';
    // プライマリーキー
    protected $primaryKey = 'hospital_id';
    // プライマリーキーの型
    protected $keyType = 'string';
    // auto increment利用しない
    public $incrementing = false;
    // ソフトデリート利用する
    use SoftDeletes;

    protected $fillable = [
        "hospital_id",
        "hospital_type",
        "dummy"
    ];

    public function organization()
    {
        return $this->hasOne('App\Models\Organization', 'organization_id', 'hospital_id');
    }
}
