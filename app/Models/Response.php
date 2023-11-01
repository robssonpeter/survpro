<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $primaryKey = 'response_id';

    protected $fillable = ['question_id', 'response_text'];

    public function question() {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
