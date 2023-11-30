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
        'time_reply'
    ];
    protected $casts=[
        'user_id'=>'integer',
        'exam_id'=>'integer',
        'time_reply'=>'integer'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function exam(): HasOne
    {
        return $this->hasOne(Exam::class, 'id', 'exam_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamResultAnswer::class, 'result_id', 'id');
    }

    public function correctAnswers(): int
    {
        return $this->answers->where('result', 1)->count();
    }
    public function marked(){
        return $this->hasMany(MarkQuestions::class,'exam_result_id','id');
    }
}
