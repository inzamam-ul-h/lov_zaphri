<?php

namespace Database\Seeders;

use App\Models\ContactDetail;
use Illuminate\Database\Seeder;

class ContactDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContactDetail::create([
            'user_id' => 1,
            'about_zaphry' => 'Sports training is a special process of preparation of sportspersons based on scientific principles aimed at improving and maintaining higher perform',
            'phone' => '0303030303',
            'email' => 'info@zaphri.com',
            'address' => 'a street b square America',
            'whatsapp' => 'whatsapp.com',
            'facebook' => 'Facebook.com',
            'twitter' => 'Twitter.com',
            'dribble' => 'Dribble.com',
            'linkdin' => 'Linkdin.com',
            'youtube' => 'Youtube.com',
            'created_by' => 1,
            'created_at' => '2021-06-29 05:37:43',
        ]);
    }
}
