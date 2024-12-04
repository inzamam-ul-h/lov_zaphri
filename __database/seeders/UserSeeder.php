<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserCalendar;
use App\Models\UserPersonal;
use App\Models\UserEducation;
use App\Models\UserProfessional;
use App\Models\Module;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$phone = 923335000000;
		{
			$role = 'admin';

			// Create new Super Admin User
			{
				$name = 'System User';
				$email = 'developer@logic-valley.com';
				$phone++;

				$this->create_admin_user($role, $name, $email, $phone);
			}
		}
		
		{
			$role = 'super_admin';

			// Create new Super Admin User
			{
				$name = 'Super Admin';
				$email = 'admin@gmail.com';
				$phone++;

				$this->create_admin_user($role, $name, $email, $phone);
			}
		}
    }

	private function create_admin_user($role, $name, $email, $phone)
	{
		$created_by = 1;
		$user_type = 0;
		$password = "admin12345";

		$model = new User();
			$model->user_type = $user_type;
			$model->name = $name;
			$model->email = $email;
			$model->password = bcrypt($password);
			$model->phone = "+".$phone;
			$model->phone_no_verified = "+92";
			$model->email_verified = 1;
			$model->profile_status = 1;
			$model->verified = 1;
			$model->admin_approved = 1;
			$model->status = 1;
            $model->created_by = $created_by;
		$model->save();

		$user_id = $model->id;

		// Assign role to user
		$user = User::find($user_id);
		$user->assignRole($role);
	}
}
