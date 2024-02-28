<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'logs';
    protected $fillable = [
        'id', 'origin', 'description', 'date'
    ];
}
