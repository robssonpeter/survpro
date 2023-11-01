<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyVersion extends Model
{
    use HasFactory;

    protected $primaryKey = 'version_id';

    protected $fillable = ['survey_id', 'version_number'];

    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
}
