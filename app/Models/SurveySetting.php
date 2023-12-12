<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySetting extends Model
{
    use HasFactory;

    protected $table = 'survey_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = ['survey_id', 'key', 'value'];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'survey_id');
    }
}
