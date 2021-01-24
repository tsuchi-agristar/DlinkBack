<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * 全サービス取得
     *
     * @param ServiceRequest $request
     * @return void
     */
    public function getService(ServiceRequest $request)
    {
        $response_data = null;
        \Log::debug('[getService] ' . 'Function: ' . __FUNCTION__);
        $model = Service::
            orderBy("service_type", "asc")
            ->orderBy("school_number", "asc")
            ->orderBy("location", "asc")
            ->orderBy("fair_format", "asc")
            ->get();
        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }

}
