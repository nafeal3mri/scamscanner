<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainList extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $fillable = [
        'domain_url',
        'main_domain',
        'type',
        'category',
        'page_title',
        'page_icon',
        'description'
    ];

    public function categ()
    {
        return $this->belongsTo(DomainCategor::class,'category');
    }
    
}
