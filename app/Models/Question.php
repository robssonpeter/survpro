<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';

    protected $fillable = ['survey_id', 'question_text', 'question_type', 'options', 'is_required', 'index_number'];

    public static $types = [
        ['type' => 'number', 'label' => 'Number'],
        ['type' => 'short-answer', 'label' => 'Short Answer'],
        ['type' => 'paragraph', 'label' => 'Paragraph'],
        ['type' => 'multiple-choice', 'label' => 'Multiple Choice'],
        ['type' => 'checkbox', 'label' => 'Checkbox'],
        ['type' => 'drop-down', 'label' => 'Dropdown'],
        ['type' => 'linear-scale', 'label' => 'Linear Scale'],
        ['type' => 'file-upload', 'label' => 'File Upload'],
        ['type' => 'date', 'label' => 'Date'],
        ['type' => 'time', 'label' => 'Time']
    ];

    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function responses() {
        return $this->hasMany(Response::class, 'question_id');
    }
}
