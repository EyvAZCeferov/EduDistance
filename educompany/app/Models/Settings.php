<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    protected $table='settings';
    protected $fillable=['name','description','address','address_2','social_media','logo','logo_white'];
    protected $casts=['name'=>"json",'description'=>"json",'address'=>"json",'address_2'=>'json','social_media'=>"json"];
}
