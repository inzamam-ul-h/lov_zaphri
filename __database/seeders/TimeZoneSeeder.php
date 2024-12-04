<?php

namespace Database\Seeders;

use App\Models\TimeZone;
use Illuminate\Database\Seeder;

class TimeZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezones = [
            [
                'name' => 'EST',
                'display_name' => 'Eastern Standard Time (EST)',
            ],
            [
                'name' => 'CST6CDT',
                'display_name' => 'Central Standard Time (CST)',
            ],
            [
                'name' => 'PST8PDT',
                'display_name' => 'Pacific Standard Time (PST)',
            ],
            [
                'name' => 'MST',
                'display_name' => 'Mountain Standard Time (MST)',
            ],
            [
                'name' => 'Asia/Karachi',
                'display_name' => 'Asia/Karachi (GMT+05:00)',
            ],
        ];
        
        foreach ($timezones as $timezone) {
            $model = new TimeZone();
            
            $model->name = $timezone['name'];
            $model->display_name = $timezone['display_name'];
            $model->created_by = 1;
            $model->save();
            
        }
        
    }
}
