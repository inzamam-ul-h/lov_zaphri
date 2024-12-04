<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$country_id = 35; //canada
		
		$array = array();
			$array['AB'] = 'Alberta';
			$array['BC'] = 'British Columbia';
			$array['MB'] = 'Manitoba';
			$array['NB'] = 'New Brunswick';
			$array['NL'] = 'Newfoundland and Labrador';
			$array['NT'] = 'Northwest Territories';
			$array['NS'] = 'Nova Scotia';
			$array['NU'] = 'Nunavut';
			$array['ON'] = 'Ontario';
			$array['PE'] = 'Prince Edward Island';
			$array['QC'] = 'Quebec';
			$array['SK'] = 'Saskatchewan';
			$array['YT'] = 'Yukon';
		
		
		foreach($array as $code => $name)
		{				
			$model = new State();
				$model->country_id = $country_id;
				$model->name = $name;
				// $model->name_fr = $name;
				// $model->code = $code;
				$model->created_by = "1";
				$model->save();
		}
    }
}