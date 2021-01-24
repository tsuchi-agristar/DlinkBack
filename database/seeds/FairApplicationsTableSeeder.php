<?php

use Illuminate\Database\Seeder;

use App\Models\FairApplication;
class FairApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(FairApplication::class, 10)->create();
    }
}
