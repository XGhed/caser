<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $guarded = [

    		'id',
    		'created_at',
    		'updated_at'
    	];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
