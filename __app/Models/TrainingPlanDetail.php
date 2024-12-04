<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingPlanDetail extends Model
{
	use HasFactory;
	
	use SoftDeletes;
	
    public $table = 'training_plan_details';
}
