<?php

use Illuminate\Database\Seeder;

use App\Models\Service;
use Faker\Factory as Faker;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $service = [
            [$faker->uuid, 1, null, null, 1,  30000], //基本料金, 関東・関西
            [$faker->uuid, 1, null, null, 2,  10000], //基本料金, 関東・関西以外
            [$faker->uuid, 2, null, null, 1,  30000], //オプション料金(個別形式使い放題), 関東・関西
            [$faker->uuid, 2, null, null, 2,  20000], //オプション料金(個別形式使い放題), 関東・関西以外
            [$faker->uuid, 3, 1   , 1   , 1, 200000], //オプション料金, 授業,   同時1, 関東・関西
            [$faker->uuid, 3, 2   , 1   , 1, 100000], //オプション料金, 小人数, 同時1, 関東・関西
            [$faker->uuid, 3, 3   , 1   , 1,      0], //オプション料金, 個別,   同時1, 関東・関西
            [$faker->uuid, 3, 1   , 1   , 2, 100000], //オプション料金, 授業,   同時1, 関東・関西以外
            [$faker->uuid, 3, 2   , 1   , 2,  50000], //オプション料金, 小人数, 同時1, 関東・関西以外
            [$faker->uuid, 3, 3   , 1   , 2,      0], //オプション料金, 個別,   同時1, 関東・関西以外
            [$faker->uuid, 3, 1   , 2   , 1, 300000], //オプション料金, 授業,   同時2, 関東・関西
            [$faker->uuid, 3, 2   , 2   , 1, 150000], //オプション料金, 小人数, 同時2, 関東・関西
            [$faker->uuid, 3, 3   , 2   , 1,      0], //オプション料金, 個別,   同時2, 関東・関西
            [$faker->uuid, 3, 1   , 2   , 2, 150000], //オプション料金, 授業,   同時2, 関東・関西以外
            [$faker->uuid, 3, 2   , 2   , 2,  75000], //オプション料金, 小人数, 同時2, 関東・関西以外
            [$faker->uuid, 3, 3   , 2   , 2,      0], //オプション料金, 個別,   同時2, 関東・関西以外
        ];

        $serviceData = array();
        foreach ($service as $data) {
               $serviceData[] = array(
                   'service_id'    => $data[0],
                   'service_type'  => $data[1],
                   'fair_format'   => $data[2],
                   'school_number' => $data[3],
                   'location'      => $data[4],
                   'price'         => $data[5],
            );
        }
        Service::insert($serviceData);
    }
}
