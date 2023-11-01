<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $primaryKey = 'template_id';

    protected $fillable = ['title', 'description', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
