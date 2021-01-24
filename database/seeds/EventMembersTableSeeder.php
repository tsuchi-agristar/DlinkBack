<?php

use Illuminate\Database\Seeder;

use App\Models\EventMember;
class EventMembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(EventMember::class, 10)->create();
    }
}
