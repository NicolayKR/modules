<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyName extends Model
{
    use HasFactory;

    public function getUrl(){
        
    }
    protected $fillable = [
        'name',
        'xml_feed',
        'cyan_key',
        'user_id',
    ];
}
