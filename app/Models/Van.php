<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Van extends Model
{
    //
    protected $fillable =[
        "VanID",
        "NumberPlate",
        "Longitude",
        "Latitude",
        "VanOperatorID",
        "DriverID",
    ];
}
