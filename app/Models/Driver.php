<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    // defined primary key
    protected $primaryKey = 'DriverID';
    public $incrementing = true;

    protected $fillable = [
        "DriverID",
        "DriverName",
        "DriverPermit",
    ];

}
