<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital;
use App\Models\Organization;
use App\Models\Fair;
use App\Models\HospitalAppend;
use App\Http\Requests\HospitalRequest;

class HospitalController extends Controller
{

    /**
     * 全病院情報を取得する
     *
     * @param HospitalRequest $request
     * @return void
     */
    public function getHospital(HospitalRequest $request)
    {
        $response_data = null;
        \Log::debug('[病院取得] ' . 'Function: ' . __FUNCTION__);
        $model = Organization::typeHospital()->with('hospital')
            ->with('user')
            ->orderBy("created_at", "desc")
            ->get();
        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }


    /**
     * 病院登録
     *
     * @param HospitalRequest $request
     * @return void
     */
    public function addHospital(HospitalRequest $request)
    {
        $response_data = null;

        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));
        DB::beginTransaction();
        try {
            // insert処理開始
            $hospital = Hospital::create($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data["data"]["error"][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        DB::commit();
        $response_data = $hospital;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したIDをもつ病院情報取得API
     *
     * @param Request $request
     * @param string $hospital_id
     * @return void
     */
    public function getHospitalById(HospitalRequest $request, string $hospital_id)
    {
        $response_data = null;
        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] hospital_id=' . $hospital_id);

        $model = Hospital::withTrashed()->find($hospital_id);

        // 削除済みも含めレコードが見つからない場合
        if (empty($model)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }

        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }


    /**
     * 指定した病院情報レコードをアップデート
     *
     * @param Request $request
     * @param integer $hospital_id
     * @return void
     */
    public function updateHospitalById(HospitalRequest $request, string $hospital_id)
    {

        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] hospital_id=' . $hospital_id);
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            $hospital = Hospital::find($hospital_id);
            $hospital->fill($request->all())->save();
        } catch (\Exception $e) {
            // トランザクション失敗
            DB::rollback();
            \Log::critical($e);
            $response_data["data"]["error"][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        $response_data = $hospital;
        return \Response::json($response_data, Response::HTTP_OK);

    }

    /**
     * 病院と組織の論理削除処理
     *
     * @param HospitalRequest $request
     * @param string $hospital_id
     * @return void
     */
    public function deleteHospitalById(HospitalRequest $request, string $hospital_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] hospital_id=' . $hospital_id);

        DB::beginTransaction();
        try {
            $hospital = Hospital::find($hospital_id);

            // 削除対象のレコードが見つからない場合
            if (empty($hospital)) {
                DB::rollback();
                $response_data = null;
                return \Response::json($response_data, Response::HTTP_OK);
            }

            // 病院テーブルから論理削除
            $data = $hospital->delete();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data["data"]["error"][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        $response_data = true;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定した病院の付属情報を取得する
     *
     * @param HospitalRequest $request
     * @param string $hospital_id
     * @return JsonResponse
     */
    public function getHospitalAppend(HospitalRequest $request, string $hospital_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] hospital_id=' . $hospital_id);

        $hospital = Hospital::find($hospital_id);
        if (empty($hospital)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }

        $response_data = HospitalAppend::with([
            "hospital_intership",
            "hospital_practice",
            "hospital_scholarship",
            "hospital_fair"
        ])->where("hospital_id", $hospital_id)
        ->withTrashed()
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定した病院の病院説明会取得
     *
     * @param HospitalRequest $request
     * @param string $hospital_id
     * @return void
     */
    public function getHospitalFair(HospitalRequest $request, string $hospital_id)
    {
        \Log::debug('['.__FUNCTION__.'] hospital_id=' . $hospital_id);
        $response_data = null;
        $response_data = Fair::with([
            "fair_type",
            "online_events",
            "online_events.estimate",
            "fair_applications"
        ])
        ->where("hospital_id", $hospital_id)
        ->withTrashed()
        ->orderBy("created_at","desc")
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
