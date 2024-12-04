<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $experiances = [
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
        foreach ($experiances as $key => $group) {
            $model = new Experience();
            $model->title = $group;
            $model->save();
        }
    }
}
