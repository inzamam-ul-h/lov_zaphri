<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ageGroups = [
            'Any',
            'Under 10',
            '10 - 11',
            '11 - 12',
            '12 - 13',
            '13 - 14',
            '14 - 15',
            '15 - 16',
            '16 - 17',
            '17 - 18',
            'Above 18',
        ];
        foreach ($ageGroups as $key => $group) {
            $model = new AgeGroup();
            $model->title = $group;
            $model->save();
        }

    }
}
