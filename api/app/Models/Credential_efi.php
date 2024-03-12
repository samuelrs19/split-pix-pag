<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential_efi extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'credential_efi';
    protected $fillable = [
        'id', 'client_Id', 'client_Secret', 'environment'
    ];
    public $timestamps = false;
}
