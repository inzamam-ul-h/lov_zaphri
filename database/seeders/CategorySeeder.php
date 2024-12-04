<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$array = array();
			$array[] = 'Juggling';
			$array[] = 'Kicking';
			$array[] = 'Dribbling';
			$array[] = 'Speed';
			$array[] = 'Agility';
			$array[] = 'Control';
			$array[] = 'Moves';
			
		
		
		foreach($array as $name)
		{				
			$model = new Category();
				$model->name = $name;
				// $model->name_fr = $name;
				$model->created_by = "1";
				$model->save();
		}
    }
}