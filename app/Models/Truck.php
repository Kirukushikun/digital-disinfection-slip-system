<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Truck extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'plate_number',
        'disabled',
    ];

    public function disinfectionSlips()
    {
        return $this->hasMany(DisinfectionSlip::class);
    }
}
