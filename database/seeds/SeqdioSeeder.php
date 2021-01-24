<?php

use Illuminate\Database\Seeder;

use App\Models\Organization;
use App\Models\User;

class SeqdioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $organization = Organization::create(
            [
                'organization_id' => '11111111-1111-1111-1111-111111111111',
                'organization_type' => 1,
                'organization_name' => "Seqdio",
                'organization_name_kana' => "セクディオ",
                'prefecture' => "福岡県",
                'city' => "福岡市",
                'address' => "博多区下呉服町4-31 ハンドレッドビル",
                'homepage' => "https://seqdio.com/",
                'dummy' => false
            ]
        );

        $user = User::create(
            [
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'organization_id' => $organization->organization_id,
                'mail_address' => "dlink@seqdio.com",
                'account_name' => "SeqdioAdmin777",
                'password' => "Password!",
            ]
        );
    }
}
