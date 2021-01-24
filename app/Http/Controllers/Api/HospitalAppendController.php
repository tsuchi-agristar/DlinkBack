<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\HospitalAppendRequest;
use App\Models\HospitalAppend;

// fairへ統合のため不要
class HospitalAppendController extends Controller
{
    public function createHospitalAppend(HospitalAppendRequest $request)
    {
        $response_data = null;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    public function updateHospitalAppend(HospitalAppendRequest $request)
    {
        $response_data = null;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    public function deleteHospitalAppendById(HospitalAppendRequest $request, $append_information_id)
    {
        $response_data = null;
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
