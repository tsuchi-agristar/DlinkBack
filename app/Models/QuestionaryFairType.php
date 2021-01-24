<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionaryFairType extends Model
{

    //
    protected $table = 'QuestionaryFairTypes';

    protected $primaryKey = null;

    protected $fillable = [
        'questionary_id',
        'fair_type'
    ];

    public $incrementing = false;
    public $timestamps = false;

    // 説明会アンケート
    public function questionary()
    {
        return $this->belongsTo(Questionary::class, 'questionary_id', 'questionary_id');
    }
}
