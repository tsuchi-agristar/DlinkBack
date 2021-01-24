<?php

use Illuminate\Database\Seeder;

use App\Models\Organization;
use App\Models\User;
use Faker\Factory as Faker;

class FiveDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        
        $organization = Organization::create(
            [
                'organization_id' => $faker->uuid,
                'organization_type' => 1,
                'organization_name' => "5D",
                'organization_name_kana' => "ファイブデー",
                'prefecture' => "福岡県",
                'city' => "福岡市",
                'address' => "博多区下呉服町4-31 ハンドレッドビル",
                'homepage' => "https://5d.com/",
                'dummy' => false
            ]
        );

        $user = User::create(
            [
                'user_id' => $faker->uuid,
                'organization_id' => $organization->organization_id,
                'mail_address' => "admin@5d.com",
                'account_name' => "fivedd",
                'password' => "Password!",
            ]
        );
    }
}
