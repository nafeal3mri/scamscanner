<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainList extends Model
{
    use HasFactory;

    public function categ()
    {
        return $this->belongsTo(DomainCategor::class,'category');
    }
    
}