<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeZone extends Model
{
	use HasFactory;
	
	//use SoftDeletes;
	
    public $table = 'time_zones';
}
