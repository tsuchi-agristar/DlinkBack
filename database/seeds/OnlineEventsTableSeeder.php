<?php

use Illuminate\Database\Seeder;

use App\Models\OnlineEvent;
class OnlineEventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(OnlineEvent::class, 10)->create();
    }
}
