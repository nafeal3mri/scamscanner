<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportMistakes extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    public function aprove_request($crud = false)
    {
        return '<a class="btn btn-sm btn-link text-success" target="_blank" href="http://google.com?q='.urlencode($this->text).'"><i class="la la-check"></i> Move to list</a>';
    }
    public function reject_request($crud = false)
    {
        return '<button type="submit" class="btn btn-sm btn-link text-danger" target="_blank" href=""><i class="la la-times"></i> Ignore</button>';
    }
}
