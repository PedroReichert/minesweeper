<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function marks(){
       return $this->hasMany(Mark::class);
    }

    public function user(){
       return $this->belongsTo(User::class);
    }
}
