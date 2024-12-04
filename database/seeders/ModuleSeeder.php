<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Module;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = array();
			$array[] = 'Sessions';
			$array[] = 'Bookings';
			$array[] = 'Payments';
			$array[] = 'Training Plans';
			$array[] = 'Training Programs';
			$array[] = 'Videos';
			$array[] = 'Events';
			$array[] = 'Teams';
        	$array[] = 'Team Members';
			$array[] = 'Club Members';
			//$array[] = 'Timezones';
			//$array[] = 'Countries';
			//$array[] = 'Provinces';
			//$array[] = 'Cities';
			$array[] = 'Users';
			$array[] = 'Settings';
			$array[] = 'Categories';
			$array[] = 'Roles';
			$array[] = 'Modules';

		$type = 0;
		foreach($array as $name)
		{
			$this->common($name, $type);
		}
    }

    public function common($name, $type)
    {
        $exists = 0;
		$records = Module::select(['id'])->where('module_name', '=', $name)->limit(1)->get();
        foreach($records as $record)
        {
            $exists = 1;
        }
        if($exists == 0)
        {
            $model = new Module();

                $model->module_name = $name;
                $model->type = $type;
                $model->mod_list = 1;
                $model->mod_add = 1;
                $model->mod_edit = 1;
                $model->mod_view = 1;
                $model->mod_status = 1;
                $model->mod_delete = 1;
                $model->created_by = "1";

            $model->save();

            $permission = $name.'-listing';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);



            $permission = $name.'-add';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);



            $permission = $name.'-edit';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);



            $permission = $name.'-view';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);



            $permission = $name.'-status';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);



            $permission = $name.'-delete';
            $permission = createSlug($permission);
            Permission::findOrCreate($permission);
        }

    }
}
