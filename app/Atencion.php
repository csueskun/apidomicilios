<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    protected $table = "atencion";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
}
