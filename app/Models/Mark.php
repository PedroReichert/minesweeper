<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $guarded = [];

    const MINE = 'MINE';
    const FLAG = 'FLAG';
    const QUESTION = 'QUESTION';
    const LABEL = 'LABEL';

    public function field(){
        return $this->belongsTo(Field::class);
    }

    
}
