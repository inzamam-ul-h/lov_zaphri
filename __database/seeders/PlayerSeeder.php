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

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$str = 'Lionel Messi
Cristiano Ronaldo
Xavi
Andres Iniesta
Zlatan Ibrahimovic
Radamel Falcao
Robin van Persie
Andrea Pirlo
Yaya Toure
Edinson Cavani
Sergio Aguero
Iker Casillas
Neymar
Sergio Busquets
Xabi Alonso
Thiago Silva
Mesut Ozil
David Silva
Bastian Schweinsteiger
Gianluigi Buffon
Luis Suarez
Sergio Ramos
Vincent Kompany
Gerard Pique
Philipp Lahm
Willian
Marco Reus
Franck Ribery
Manuel Neuer
Ashley Cole
Wayne Rooney
Juan Mata
Thomas Muller
Mario Götze
Karim Benzema
Cesc Fabregas
Oscar
Fernandinho
Javier Mascherano
Gareth Bale
Javier Zanetti
Daniele De Rossi
Dani Alves
Petr Cech
Mats Hummels
Carles Puyol
Angel Di Maria
Carlos Tevez
Didier Drogba
Giorgio Chiellini
Marcelo
Stephan El Shaarawy
Toni Kroos
Samuel Eto’o
Jordi Alba
Mario Gomez
Arturo Vidal
Eden Hazard
James Rodriguez
Marouane Fellaini
Ramires
David Villa
Klaas Jan Huntelaar
Nemanja Vidic
Joe Hart
Arjen Robben
Mario Balotelli
Mathieu Valbuena
Pierre-Emerick Aubameyang
Robert Lewandowski
Hernanes
Pedro
Santi Cazorla
Christian Eriksen
Ezequiel Lavezzi
Joao Moutinho
Mario Mandžukić
Patrice Evra
David Luiz
Luka Modric
Victor Wanyama
Mapou Yanga-MBiwa
Hulk
Darijo Srna
Emmanuel Mayuka
John Terry
Kwadwo Asamoah
Leonardo Bonucci
Javier Pastore
Henrikh Mkhitaryan
Moussa Dembele
Hatem Ben Arfa
Samir Nasri
Shinji Kagawa
Wesley Sneijder
Pepe
Marek Hamsik
Javi Martinez
Diego Forlan
Paulinho';
		$array = explode("\r", $str);
		
        // players		
        {
            $phone = 923338000000;
			$i = 0;
			foreach($array as $user){
				$i++;
				$name = str_replace(' ', '-', trim($user));
				$name.= ' Player';
				$email = 'player'.$i.'@gmail.com';
				$phone++;
	
				$this->create_player_user($name, $email, $phone);
			}
        }
    }
	
	private function create_player_user($name, $email, $phone)
	{		
		$role = 'player';
		$created_by = 1;
		$user_type = 2;
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
		$dob = '1996-09-18';

		$UserPersonal = new UserPersonal();
			$UserPersonal->user_id = $user_id;
			$UserPersonal->first_name = $first_name;
			$UserPersonal->last_name = $last_name;
			$UserPersonal->dob = $dob;
			$UserPersonal->gender = 'Male';
			$UserPersonal->zip_code = '46000';
			$UserPersonal->about_me = 'Hi, I am '.$name.', a professional player';
            $UserPersonal->created_by = $user_id;
		$UserPersonal->save();
		
		$UserProfessional = new UserProfessional();
			$UserProfessional->user_id = $user_id;
            $UserProfessional->created_by = $user_id;
		$UserProfessional->save();
		
		$UserEducation = new UserEducation();
			$UserEducation->user_id = $user_id;
            $UserEducation->created_by = $user_id;
		$UserEducation->save();
		
		$time_zone = 1;
		$UserCalendar = new UserCalendar();
			$UserCalendar->user_id = $user_id;
			$UserCalendar->time_zone = $time_zone;
            $UserCalendar->created_by = $user_id;
		$UserCalendar->save();
	}
}