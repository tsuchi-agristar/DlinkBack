<?php

use Illuminate\Database\Seeder;

use App\Models\Fair;
class FairsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Fair::class, 10)->create();
    }
}
