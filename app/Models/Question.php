<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';

    protected $fillable = ['survey_id', 'question_text', 'question_type'];

    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function responses() {
        return $this->hasMany(Response::class, 'question_id');
    }
}
