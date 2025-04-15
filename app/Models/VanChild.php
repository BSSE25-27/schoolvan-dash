<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanChild extends Model
{
    //
    protected $fillable = [
        'VanID',
        'ChildID',
    ];

    public function vanChildren () {
        return $this->hasMany(VanChild::class, 'VanID', 'VanID');
    }

}
