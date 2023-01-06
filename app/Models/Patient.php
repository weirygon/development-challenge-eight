<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $grarded = [];

    public function exams(){

        return $this->hasMany(Exam::class);
    }

    public function doctor(){
        
        return $this->belongsTo(Doctor::class);
    }
}
