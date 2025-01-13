<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filament extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'route_coordinates'
    ];

    protected $casts=[
        'route_coordinates'=>'array'
    ];
}
