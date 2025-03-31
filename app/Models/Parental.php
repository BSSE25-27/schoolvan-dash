<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parental extends Model
{
    //
    protected $primaryKey = 'ParentID';
    public $incrementing = true;

    protected $fillable = [
        "ParentID",
        "ParentName",
        "Longitude",
        "Latitude",
        "Address",
        "PhoneNumber",
        "Email",
    ];
}
