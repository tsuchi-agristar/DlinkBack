<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionaryPlace extends Model
{

    //
    protected $table = 'QuestionaryPlaces';

    protected $primaryKey = null;

    protected $fillable = [
        'questionary_id',
        'place'
    ];

    public $incrementing = false;
    public $timestamps = false;

    // 説明会アンケート
    public function questionary()
    {
        return $this->belongsTo(Questionary::class, 'questionary_id', 'questionary_id');
    }
}
