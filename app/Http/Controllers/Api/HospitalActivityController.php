<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\HospitalActivityRequest;

use App\Models\Organization;
class HospitalActivityController extends Controller
{
    /**
     * 病院アクティビティ取得
     *
     * @param HospitalActivityRequest $request
     * @return void
     */
    public function getHospitalActivity(HospitalActivityRequest $request)
    {
        $response_data = null;
        $response_data = Organization::with([
            "hospital",
            "eventMembers",
            "eventMembers.onlineEvent" => function ($query) {
                $query->orderBy("created_at", "desc");
            },
            "fair" => function ($query) {
                $query->orderBy("created_at", "desc");
            },
            "fair.fair_type",
            "fair.online_events"
        ])
        ->where("organization_type", Config("const.ORGANIZATION_TYPE.HOSPITAL"))
        ->orderBy("created_at", "desc")
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
