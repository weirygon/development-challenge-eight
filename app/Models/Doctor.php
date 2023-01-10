<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{

    use HasFactory;

    protected $grarded = [];

    public function patients(){

        return $this->hasMany(Patient::class);
    }

    public function user(){
        
        $this->belongsTo(User::class);
    }

}
