<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // $this->call(OrganizationsTableSeeder::class);
        // $this->call(SchoolsTableSeeder::class);
        // $this->call(HospitalsTableSeeder::class);
        // $this->call(FairsTableSeeder::class);
        // $this->call(OnlineEventsTableSeeder::class);
        // $this->call(EventMembersTableSeeder::class);
        // $this->call(FairApplicationsTableSeeder::class);
        // $this->call(EstimatesTableSeeder::class);
        // $this->call(ServicesTableSeeder::class);
        // $this->call(FiveDSeeder::class);
        $this->call(SeqdioSeeder::class);
    }
}
