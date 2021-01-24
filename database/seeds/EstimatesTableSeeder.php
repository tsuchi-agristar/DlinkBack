<?php

use Illuminate\Database\Seeder;
use App\Models\Estimate;
class EstimatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Estimate::class, 10)->create();
    }
}
