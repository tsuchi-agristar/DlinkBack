<?php

use Illuminate\Database\Seeder;

use App\Models\Hospital;
class HospitalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Hospital::class, 10)->create();
    }
}
