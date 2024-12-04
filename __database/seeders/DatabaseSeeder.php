<?php

namespace Database\Seeders;

use App\Models\CaConditionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
			
			CountrySeeder::class,
			
			StateSeeder::class,
			
			CitySeeder::class,
			
			TimeZoneSeeder::class,
			
			TemplateSeeder::class,
			
			CategorySeeder::class,
			
			ModuleSeeder::class,
			
			RoleSeeder::class,
			
			UserSeeder::class,
			
			CoachSeeder::class,
			
			PlayerSeeder::class,
			
			ClubSeeder::class,
			
			AssociationSeeder::class,
			
			SessionTypeSeeder::class,
			
			ContactDetailSeeder::class,

			GeneralTableSeeder::class,

			AgeGroupSeeder::class,

			ExperienceSeeder::class,

		]);
    }
}
