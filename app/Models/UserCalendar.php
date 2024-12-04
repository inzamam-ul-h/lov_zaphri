<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCalendar extends Model
{
	use HasFactory;
	
	use SoftDeletes;
	
    public $table = 'user_calendars';

	protected $guarded = ['id']; 
}
