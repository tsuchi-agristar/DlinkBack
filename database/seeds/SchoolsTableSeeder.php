<?php

use Illuminate\Database\Seeder;

use App\Models\School;
class SchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           factory(School::class, 10)->create();
    }
}
