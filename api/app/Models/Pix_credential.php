<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pix_credential extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'pix_credential';
    protected $fillable = [
        'id', 'payer_key', 'payer_name', 'key_favored_one', 'key_favored_two', 'key_favored_two' 
    ];
    public $timestamps = false;
}
