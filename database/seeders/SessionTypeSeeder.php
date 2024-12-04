<?php

namespace Database\Seeders;

use App\Models\SessionType;
use Illuminate\Database\Seeder;

class SessionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sessionTypeArray = [
            'General',
            'Juggling',
            'Kicking',
            'Dribbling',
            'Speed',
            'Agility',
            'Control',
            'Moves',
        ];
        foreach ($sessionTypeArray as $key => $type) {
            $model = new SessionType();
            $model->name = $type;
            $model->created_by = 1;
            $model->save();
        }
    }
}
