<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ExamQuestion extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'exam_questions';

     CONST TYPES = [
         'Tək Seçimli' => 1,
         'Çox Seçimli' => 2,
         'Açıq' => 3,
         'Uzlaşma' => 4,
     ];

    public const ALLOWED_FILE_SIZE_KB = 20 * 1024;

    public const ALLOWED_FILE_MIMES = [
        // taken from https://www.freeformatter.com/mime-types-list.html
        'image/jpeg', 'image/x-citrix-jpeg', // jpeg, jpg
        'image/png', 'image/x-citrix-png', 'image/x-png', // png
        'image/gif', // gif
        'image/svg+xml', // svg
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaCollection('exam_question')
            ->acceptsMimeTypes(self::ALLOWED_FILE_MIMES)
            ->acceptsFile(fn (File $file) => $file->size <= self::ALLOWED_FILE_SIZE_KB)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->nonQueued()
                    ->width(270);
            });
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'question_id', 'id');
    }

    public function correctAnswer()
    {
        if ($this->type == 1) {
            return $this->answers?->where('correct', true)?->first();
        } elseif ($this->type == 2) {
            return $this->answers?->where('correct', true);
        }elseif ($this->type == 3) {
            return $this->answers?->where('correct', true)->first();
        }else{
            return $this->answers?->where('correct', true)?->first();
        }
    }

    public function section(){
        return $this->hasOne(Section::class,'id','section_id');
    }
}
