<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanCond extends Model
{
    use HasFactory;

    public function domainsList()
    {
        return $this->belongsTo(DomainList::class,'domain_id');
    }
    public function domainMessage()
    {
        return $this->belongsTo(CondsMessages::class,'cond_message_id');
    }
}
