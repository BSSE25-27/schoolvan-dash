<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    //
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        
        'is_started' => 'boolean',
        'is_complete' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
