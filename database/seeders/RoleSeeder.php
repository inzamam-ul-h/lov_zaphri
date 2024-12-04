<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Module;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new Admin Roles
		{	
			$role = $this->get_role('admin');	
	
			// Assign permissions to role
			$Modules = Module::where('id', '>', 0)->get();
			foreach($Modules  as $Module)
			{
				$permission_array = explode(':', $Module->module_name);
				$this->common($role, $Module, $permission_array);
			}
		}
		
        // Create new Super Admin Roles
		{
			$role = $this->get_role('super_admin');
			
			$array = array();
				$array[] = 'Sessions:list:view';
				$array[] = 'Bookings:list:view';
				$array[] = 'Payments:list:view';
				$array[] = 'Training Plans:list:view';
				$array[] = 'Training Programs:list:view';
				$array[] = 'Videos:list:view';
				$array[] = 'Events:list:view';
				$array[] = 'Teams:list:view';
				$array[] = 'Users';
				$array[] = 'Settings';
				$array[] = 'Categories';
			
			$this->common_others($role, $array);
		}
		
        // Create new club Role
		{
			$role = $this->get_role('club');
			
			$array = array();
				$array[] = 'Training Programs';
				$array[] = 'Videos';
				$array[] = 'Events';
				$array[] = 'Teams';
				$array[] = 'Team Members';
				$array[] = 'Club Members';
				$array[] = 'Users';
			
			$this->common_others($role, $array);
		}

        // Create new coach Role
		{
			$role = $this->get_role('coach');
			
			$array = array();
				$array[] = 'Sessions';
				$array[] = 'Bookings:list:view:status';
				$array[] = 'Payments:list:view';
				$array[] = 'Training Programs:list:view';
				$array[] = 'Training Plans';
				$array[] = 'Videos';
				$array[] = 'Events:list:view';
				$array[] = 'Teams:list:view';
				$array[] = 'Team Members';
			
			$this->common_others($role, $array);
		}

        // Create new player Role
		{
			$role = $this->get_role('player');
			
			$array = array();
				$array[] = 'Sessions:list:view';
				$array[] = 'Bookings';
				$array[] = 'Payments';
				$array[] = 'Videos:list:view';
				$array[] = 'Events:list:view';
				$array[] = 'Teams:list:view';
			
			$this->common_others($role, $array);
		}

        // Create new parent Role
		{
			$role = $this->get_role('parent');	
			
			$array = array();
				$array[] = 'Sessions:list:view';
				$array[] = 'Bookings:list:view';
				$array[] = 'Payments';
			
			$this->common_others($role, $array);
		}
    }

    public function common_others($role, $array)
    {
		foreach($array as $module_name)
		{
			$permission_array = explode(':', $module_name);
			$Modules = Module::where('module_name', '=', $permission_array[0])->get();
			foreach($Modules  as $Module)
			{
				$this->common($role, $Module, $permission_array);
			}
		}

    }

    public function common($role, $Module, $permission_array)
    {
		if((count($permission_array) > 1)){
			$Module->mod_list = (in_array('list', $permission_array)) ? 1 : 0;
			$Module->mod_add = (in_array('add', $permission_array)) ? 1 : 0;
			$Module->mod_edit = (in_array('edit', $permission_array)) ? 1 : 0;
			$Module->mod_view = (in_array('view', $permission_array)) ? 1 : 0;
			$Module->mod_status = (in_array('status', $permission_array)) ? 1 : 0;
			$Module->mod_delete = (in_array('delete', $permission_array)) ? 1 : 0;
		}
		
        $module_name = $Module->module_name;

        if($Module->mod_list == 1)
        {
			$action = "listing";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }

        if($Module->mod_add == 1)
        {
			$action = "add";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }

        if($Module->mod_edit == 1)
        {
			$action = "edit";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }

        if($Module->mod_view == 1)
        {
			$action = "view";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }

        if($Module->mod_status == 1)
        {
			$action = "status";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }

        if($Module->mod_delete == 1)
        {
			$action = "delete";
			$permission = $module_name.'-'.$action;
			$permission = createSlug($permission);
            $exits = 0;

            if($role->hasPermissionTo($permission))
            {
                $exits = 1;
            }

            if($exits == 0)
            {
                $role->givePermissionTo($permission);
            }
        }
    }
	
	public function get_role($role_name){
		$role_id = 0;
		$records = Role::select(['id'])->where('name', $role_name)->where('guard_name', 'web')->limit(1)->get();
		foreach($records as $model)
		{
			$role_id = $model->id;
		}
		if($role_id == 0)
		{
			$model = new Role();
				$model->name = $role_name;
				$model->guard_name = 'web';
				$model->display_to = 0;
			$model->save();

			$role_id = $model->id;
		}

		$role = Role::find($role_id);
		return $role;
	}

}