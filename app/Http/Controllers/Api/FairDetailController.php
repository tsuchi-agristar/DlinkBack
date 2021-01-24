<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\FairDetailRequest;
use App\Models\FairDetail;

class FairDetailController extends Controller
{

    /**
     * 説明情報の登録処理
     *
     * @param FairDetailRequest $request
     * @return void
     */
    public function createFairDetail(FairDetailRequest $request)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            $fairDetail = FairDetail::create($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        $response_data = $fairDetail;
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
