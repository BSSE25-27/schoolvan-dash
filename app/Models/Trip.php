<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    //
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'origin' => 'array',
        'destination' => 'array',
        'van_location' => 'array',
        'is_started' => 'boolean',
        'is_complete' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
