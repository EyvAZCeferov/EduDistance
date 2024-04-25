<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamResult extends Model
{
    use HasFactory;
    protected $table='exam_results';
    protected $fillable=[
        'id',
        'user_id',
        'exam_id',
        'point',
        'counted_point',
        'time_reply',
        'payed'
    ];
    protected $casts=[
        'user_id'=>'integer',
        'exam_id'=>'integer',
        'time_reply'=>'integer',
        'payed'=>'boolean'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function exam(): HasOne
    {
        return $this->hasOne(Exam::class, 'id', 'exam_id')->with('sections');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamResultAnswer::class, 'result_id', 'id');
    }

    public function correctAnswers($onlycount=true)
    {
        // Yanıtları alırken yalnızca benzersiz soru kimliği (question_id) olanları seç
        $distinctAnswers = $this->answers()->distinct('question_id')->get();

        // Seçilen benzersiz yanıtların içinden doğru olanları sayar
        if($onlycount==true)
            return $distinctAnswers->where('result', 1)->count();
        else
            return $distinctAnswers->where('result', 1);
    }

    public function wrongAnswers($onlycount=true)
    {
        // Yanıtları alırken yalnızca benzersiz soru kimliği (question_id) olanları seç
        $distinctAnswers = $this->answers()->distinct('question_id')->get();

        // Seçilen benzersiz yanıtların içinden yanlış olanları sayar
        if($onlycount==true)
            return $distinctAnswers->where('result', 0)->count();
        else
            return $distinctAnswers->where('result', 0);
    }
    public function marked(){
        return $this->hasMany(MarkQuestions::class,'exam_result_id','id');
    }
}
