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

class CoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$array = array();
			$array[] = 'Jorge Sampaoli';
			$array[] = 'Fernando Santos';
			$array[] = 'Didier Deschamps';
			$array[] = 'Arsene Wenger';
			$array[] = 'Roberto Martinez';
			$array[] = 'Leonardo Jardim';
			$array[] = 'Jupp Heynckes';
			$array[] = 'Tite Tite';
			$array[] = 'Joachim Low';
			$array[] = 'Jurgen Klopp';
			$array[] = 'Julen Lopetegui';
			$array[] = 'Ernesto Valverde';
			$array[] = 'Mauricio Pochettino';
			$array[] = 'Unai Emery';
			$array[] = 'Jose Mourinho';
			$array[] = 'Diego Simeone';
			$array[] = 'Antonio Conte';
			$array[] = 'Zinedine Zidane';
			$array[] = 'Massimiliano Allegri';
			$array[] = 'Pep Guardiola';
		
        // Coaches		
        {
            $phone = 923337000000;
			$i = 0;
			foreach($array as $user){
				$i++;
				$name = str_replace(' ', '-', trim($user));
				$name.= ' Coach';
				$email = 'coach'.$i.'@gmail.com';
				$phone++;
	
				$this->create_coach_user($name, $email, $phone);
			}
        }
    }
	
	private function create_coach_user($name, $email, $phone)
	{		
		$role = 'coach';
		$created_by = 1;
		$user_type = 1;
		$password = "User1234";
			
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
		
		//Generate Profile
		$first_name = '';
		$last_name = '';
		if(!empty($name)){
			$name_arr = explode(' ', $name);
			$first_name = $name_arr[0];
			$last_name = trim(str_replace($first_name, '', $name));
		}

		$UserPersonal = new UserPersonal();
			$UserPersonal->user_id = $user_id;
			$UserPersonal->first_name = $first_name;
			$UserPersonal->last_name = $last_name;
			$UserPersonal->meetinglink = 'https://www.zoom.com';
			$UserPersonal->gender = 'Male';
			$UserPersonal->zip_code = '46000';
			$UserPersonal->about_me = 'Hi, I am '.$name.', a professional coach';
            $UserPersonal->created_by = $user_id;
		$UserPersonal->save();
		
		$array = array();
			$array[] = 'Arsenal';
			$array[] = 'Antwerp';
			$array[] = 'Barcelona';
			$array[] = 'Bayern';
			$array[] = 'Copenhagen';
			$array[] = 'FC Porto';
			$array[] = 'Inter Milan';
			$array[] = 'Manchester City';
			$array[] = 'Manchester United';
			$array[] = 'Real Madrid';
		
		$organizational_name = rand(1, 9);
		$agegroups = rand(1, 10);
		$experience = rand(10, 25);
		$UserProfessional = new UserProfessional();
			$UserProfessional->user_id = $user_id;
			$UserProfessional->organizational_name = $array[$organizational_name];
			$UserProfessional->agegroups = $agegroups;
			$UserProfessional->experience = $experience;
            $UserProfessional->created_by = $user_id;
		$UserProfessional->save();
		
		$time_zone = 1;
		$UserCalendar = new UserCalendar();
			$UserCalendar->user_id = $user_id;
			$UserCalendar->time_zone = $time_zone;
            $UserCalendar->created_by = $user_id;
		$UserCalendar->save();
	}
}