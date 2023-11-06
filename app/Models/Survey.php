<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;
    protected $primaryKey = 'survey_id';

    protected $fillable = ['title', 'description', 'user_id', 'status'];

    protected $appends = [
        'respondents'
    ];

    public function getRespondentsAttribute(){
        return Response::where('survey_id', $this->survey_id)->groupBy('session_id')->count();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions() {
        return $this->hasMany(Question::class, 'survey_id');
    }

    public function participants() {
        return $this->hasMany(Participant::class, 'survey_id');
    }

    public function surveyVersions() {
        return $this->hasMany(SurveyVersion::class, 'survey_id');
    }
}
