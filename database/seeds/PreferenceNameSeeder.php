<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreferenceNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preference_name')->insert([
            'name' => 'crumple',
            'type' => 'BOOLEAN',
            'default' => 'false'
        ]);
        DB::table('preference_name')->insert([
            'name' => 'commission_value',
            'type' => 'NUMBER',
            'default' => '0'
        ]);
        DB::table('preference_name')->insert([
            'name' => 'starttime',
            'type' => 'TIME',
            'default' => '09:00 AM'
        ]);
        DB::table('preference_name')->insert([
            'name' => 'endtime',
            'type' => 'TIME',
            'default' => '05:00 PM'
        ]);
    }
}
