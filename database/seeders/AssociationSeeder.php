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

class AssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$coach_ids = array();
		$coach_ids[] = 0;
		
		$player_ids = array();
		$player_ids[] = 0;
		
        $clubs = User::select('id')->where('user_type', 3)->get();
		foreach($clubs as $club){
			$club_id = $club->id;
			
			$coaches = User::select('id')->where('user_type', 1)->whereNotIn('id', $coach_ids)->limit(2)->get();
			foreach($coaches as $coach){
				$coach_ids[] = $coach->id;
				$this->create_association($coach->id, $club_id);
			}
			
			$players = User::select('id')->where('user_type', 2)->whereNotIn('id', $player_ids)->limit(10)->get();
			foreach($players as $player){
				$player_ids[] = $player->id;
				$this->create_association($player->id, $club_id);
			}
		}
    }
	
	private function create_association($user_id, $club_id)
	{		
		$UserProfessional = UserProfessional::select('id')->where('user_id', $user_id)->first();
		if(!empty($UserProfessional)){
			$UserProfessional = UserProfessional::find($UserProfessional->id);
				$UserProfessional->club = $club_id;							
				$UserProfessional->club_authentication = 1;						
				$UserProfessional->updated_by = $club_id;
			$UserProfessional->save();			
		}
	}
}