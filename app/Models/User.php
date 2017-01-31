<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [

    		'id',
    		'created_at',
    		'updated_at'
    	];

    public function offices()
    {
        return $this->hasMany('App\Models\Office');
    }
}
