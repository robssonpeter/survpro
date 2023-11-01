<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $primaryKey = 'participant_id';

    protected $fillable = ['survey_id', 'participant_name', 'email', 'status'];

    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
}
