<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'name' => 'json',
        'content' => 'json',
        'price'=>'double',
        'endirim_price'=>'double'
    ];

    protected $fillable = [
        'name',
        'slug',
        'content',
        'duration',
        'point',
        'image',
        'category_id',
        'status',
        'order_number',
        'price',
        'endirim_price'
    ];


    protected $with = [
        'results',
        'category',
        'sections',
    ];

    protected static function booted()
    {
        static::addGlobalScope('active_status', function (Builder $builder) {
            $builder->where('status', 1);
        });
    }

    public function questionCount()
    {
        $count = 0;
        $qesutions = $this->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            $count += $qesution->count();
        }
        return $count;
    }

    public function questions()
    {
        return $this->sections->pluck('questions');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'exam_id', 'id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'exam_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function resultHandler()
    {
        //        $questions = count($this->questions);
        $results = ExamResult::where('exam_id', $this->id)->where('user_id', auth('users')->id())->count();

        //        if ($results < $questions) {
        //            return true;
        //        }
        return $results;
    }
}
