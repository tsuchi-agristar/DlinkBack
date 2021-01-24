<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\FairRequest;
use App\Models\Fair;
use App\Models\FairApplication;
use App\Models\FairsType;
use App\Models\OnlineEvent;
use App\Models\FairDetail;
use App\Models\HospitalAppend;
use App\Models\HospitalScholarship;
use App\Models\HospitalIntership;
use App\Models\HospitalPractice;
use App\Models\HospitalFair;
use App\Models\HospitalFairType;
use App\Models\NotificationQueue;
use Carbon\Carbon;

class FairController extends Controller
{
    /**
     * 説明会取得
     *
     * @param FairRequest $request
     * @return void
     */
    public function getFair(FairRequest $request)
    {
        $response_data = null;
        \Log::debug('[getFair] start');

        $query = Fair::whereHas('organization')->with([
                'organization',
                'organization.hospital',
                'online_events',
                'online_events.estimate',
                'online_events.event_member',
                'online_events.event_member.organization' => function ($query) {
                    // 削除済みも取得する
                    $query->withTrashed();
                },
                'fair_applications',
                'fair_type'
            ]);

        $fair_status = $request->query('fair_status');
        if ($fair_status) {
            $query = $query->whereIn('fair_status', explode(",", $fair_status));
        }

        $hospital_id = $request->query('hospital_id');
        if ($hospital_id) {
            $query = $query->where('hospital_id', $hospital_id);
        }
        $query = $query->orderBy('fairs.created_at', 'desc');

        // ページングは不要
        // $limit = $request->query('page_size') ?? config('const.DEFAULT_PAGE_SIZE');
        // $page_num = $request->query('page_num') ?? config('const.DEFAULT_PAGE_NUM');
        // $offset = (($page_num - 1) * $limit);
        // $query = $query->skip($offset)->take($limit); // offset limit双方が0の場合は、全て取得する

        $fair = $query->get();

        $response_data = $fair;
        \Log::debug('[getFair] end');
        return \Response::json($response_data, Response::HTTP_OK);
    }


    /**
     * 説明会情報の登録処理
     *
     * @param FairRequest $request
     * @return void
     */
    public function addFair(FairRequest $request)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            // 説明会テーブルへの登録処理
            $fair = Fair::create($request->all());
            $fair_id = $fair->fair_id;
            // 説明会種別情報が含まれる時
            if ($request->fair_type !== null && count($request->fair_type) > 0) {
                foreach ($request->fair_type as $index => $value) {
                    $fair->fair_type()->create($value);
                }
            }
            // POST件数分のappend_information_id
            $IDs = [];
            foreach($request->append as $index => $value) {
                // append_information_idをサーバー側で生成
                $uuid = (string)Str::uuid();
                $value["append_information_id"] = $uuid;
                $hospitalAppend = HospitalAppend::create($value);
                $IDs[] = $hospitalAppend->append_information_id;
                if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.SCHOLARSHIP")) {
                    // 奨学金情報の場合(1)
                    $value["hospital_scholarship"]["append_information_id"] = $uuid;
                    $temp = $hospitalAppend->hospital_scholarship()->create($value["hospital_scholarship"]);
                } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.INTERSHIP")) {
                    // インターンシップ情報の場合(2)
                    $value["hospital_intership"]["append_information_id"] = $uuid;
                    $temp = $hospitalAppend->hospital_intership()->create($value["hospital_intership"]);
                } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.PRACTICE")) {
                    // 実習情報の場合(3)
                    $value["hospital_practice"]["append_information_id"] = $uuid;
                    $temp = $hospitalAppend->hospital_practice()->create($value["hospital_practice"]);
                } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.FAIR")) {
                    // 説明会情報の場合(4)
                    $value["hospital_fair"]["append_information_id"]["append_information_id"] = $uuid;
                    $temp = $hospitalAppend->hospital_fair()->create($value["hospital_fair"]);
                    // 病院説明会種別
                    if ($value["hospital_fair"]["hospital_fair_type"] !== null && count($value["hospital_fair"]["hospital_fair_type"]) > 0) {
                        foreach ($value["hospital_fair"]["hospital_fair_type"] as $indexsub => $valuesub) {
                            $hospital_fair_type = [
                                "append_information_id" => $uuid,
                                "hospital_fair_type" => $valuesub
                            ];
                            $temp->hospital_fair_type()->create($hospital_fair_type);
                        }
                    }
                } else {
                    continue;
                }
                // hospital_appendの配列件数分，説明情報テーブルへ説明情報と付属情報の紐づけをする
                $fairDetail = FairDetail::create([
                    "fair_id" => $fair_id,
                    "append_information_id" => $uuid
                ]);
            }

            //通知登録
            $notificationQueue = NotificationQueue::create(
                [
                    'notification_id'   => (string)Str::uuid(),
                    'notification_type' => config('const.NOTIFICATION_TYPE.FAIR_REGISTER'),
                    'operation_id'      => $fair_id,
                    'notification_at'   => Carbon::now(),
                ]
            );
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        // POSTリクエストによってDBへ挿入されたデータを返却
        $hospitalAppend = HospitalAppend::with([
            "hospital_scholarship",
            "hospital_intership",
            "hospital_practice",
            "hospital_fair"
        ])->findMany($IDs);
        // POSTリクエストの更新結果を返却する
        $fair = Fair::with(["fair_type"])->find($request->fair_id);
        // FairオブジェクトとHospitalAppendオブジェクトを連結
        $response_data = $fair->setAttribute("append", $hospitalAppend);

        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したIDの説明会情報を取得
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return void
     */
    public function getFairById(FairRequest $request, string $fair_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);
        $fair = Fair::find($fair_id);

        if (empty($fair)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }

        $response_data = Fair::with([
            "fair_type",
            "organization" => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            }
        ])->find($fair_id);
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したIDの説明会情報を更新
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return void
     */
    public function updateFairById(FairRequest $request, string $fair_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            $fair = Fair::find($fair_id);

            //通知フラグ(ステータスが更新される場合にtrue)
            $request_fair_status = $request->fair_status;
            $notify_flg = isset($request_fair_status) ? $request_fair_status != $fair->fair_status : false;
            //病院アプリが更新する場合は常に更新とする(fair_typeの有無で判断)
            if (isset($request->fair_type)) { $notify_flg = true; }

            // スタータス整合性確認(キャンセル、募集終了からは変更不可)
            if ($fair->fair_status == Config("const.FAIR_STATUS.END")
             || $fair->fair_status == Config("const.FAIR_STATUS.CANCEL"))
            {
                //ステータスが異なる場合のみエラーとする
                if (!empty($request_fair_status) && $request_fair_status != $fair->fair_status) {
                    $response_data['message'] = 'The status cannot be updated.';
                    $response_data['errors'] = 'fair_status';
                    return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $fair->fill($request->all())->save();

            // 説明会UUIDを取得
            $fair_id = $fair->fair_id;

            // PUTリクエストの内容で処理を分ける
            if ($request->fair_type !== NULL && count($request->fair_type) === 0) {
                // bの場合
                $fair->fair_type()->delete();
            } else if ($request->fair_type !== NULL && count($request->fair_type) > 0) {
                // cの場合
                $fair->fair_type()->delete();
                foreach ($request->fair_type as $index => $value) {
                    $fair->fair_type()->create($value);
                }
            } else {
                // aの場合
            }
            $IDs = [];
            if ($request->append !== null) {
                // 指定の説明会IDから説明情報を取得
                $fairDetail = FairDetail::where("fair_id", $fair_id)->get();
                foreach ($fairDetail as $index => $value) {
                    //　DBのcascadeで関連テーブルの物理削除
                    HospitalAppend::find($value->append_information_id)->forceDelete();
                    // $hospitalAppend = HospitalAppend::find($value->append_information_id);
                    // if ((int)$hospitalAppend->append_information_type === Config("const.APPEND_INFO_TYPE.SCHOLARSHIP")) {
                    //     $temp = $hospitalAppend->hospital_scholarship()->delete();
                    // } else if ((int)$hospitalAppend->append_information_type === Config("const.APPEND_INFO_TYPE.INTERSHIP")) {
                    //     // インターンシップ情報の場合(2)
                    //     $temp = $hospitalAppend->hospital_intership()->delete();
                    // } else if ((int)$hospitalAppend->append_information_type === Config("const.APPEND_INFO_TYPE.PRACTICE")) {
                    //     // 実習情報の場合(3)
                    //     $temp = $hospitalAppend->hospital_practice()->delete();
                    // } else if ((int)$hospitalAppend->append_information_type === Config("const.APPEND_INFO_TYPE.FAIR")) {
                    //     // 説明会情報の場合(4)
                    //     $temp = $hospitalAppend->hospital_fair()->delete();
                    // }
                    // // 付属情報テーブル削除は、全付属情報種別削除後、実行
                    // $temp = $hospitalAppend->delete();
                }

                // // 説明情報テーブルを物理削除する
                // $deleteRows = FairDetail::where("fair_id", $fair_id)->delete();

                // リクエストされたデータを新規登録
                // PUT件数分のappend_information_id
                foreach($request->append as $index => $value) {
                    // append_information_idをサーバー側で生成
                    $uuid = (string)Str::uuid();
                    $value["append_information_id"] = $uuid;
                    $hospitalAppend = HospitalAppend::create($value);
                    $IDs[] = $hospitalAppend->append_information_id;
                    if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.SCHOLARSHIP")) {
                        // 奨学金情報の場合(1)
                        $value["hospital_scholarship"]["append_information_id"] = $uuid;
                        $temp = $hospitalAppend->hospital_scholarship()->create($value["hospital_scholarship"]);
                    } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.INTERSHIP")) {
                        // インターンシップ情報の場合(2)
                        $value["hospital_intership"]["append_information_id"] = $uuid;
                        $temp = $hospitalAppend->hospital_intership()->create($value["hospital_intership"]);
                    } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.PRACTICE")) {
                        // 実習情報の場合(3)
                        $value["hospital_practice"]["append_information_id"] = $uuid;
                        $temp = $hospitalAppend->hospital_practice()->create($value["hospital_practice"]);
                    } else if ((int)$value["append_information_type"] === Config("const.APPEND_INFO_TYPE.FAIR")) {
                        // 説明会情報の場合(4)
                        $value["hospital_fair"]["append_information_id"] = $uuid;
                        $temp = $hospitalAppend->hospital_fair()->create($value["hospital_fair"]);
                        // 病院説明会種別
                        if ($value["hospital_fair"]["hospital_fair_type"] !== null && count($value["hospital_fair"]["hospital_fair_type"]) > 0) {
                            foreach ($value["hospital_fair"]["hospital_fair_type"] as $indexsub => $valuesub) {
                                $hospital_fair_type = [
                                    "append_information_id" => $uuid,
                                    "hospital_fair_type" => $valuesub
                                ];
                                $temp->hospital_fair_type()->create($hospital_fair_type);
                            }
                        }
                    } else {
                        continue;
                    }
                    // hospital_appendの配列件数分，説明情報テーブルへ説明情報と付属情報の紐づけをする
                    $fairDetail = FairDetail::create([
                        "fair_id" => $fair_id,
                        "append_information_id" => $uuid
                    ]);
                }
            }

            //通知登録
            if ($fair->fair_status == Config("const.FAIR_STATUS.END")) {
                // Not Notify
            }
            else if ($fair->fair_status == Config("const.FAIR_STATUS.CANCEL")) {
                if ($notify_flg)
                {
                    $notificationQueue = NotificationQueue::create(
                        [
                            'notification_id'   => (string)Str::uuid(),
                            'notification_type' => config('const.NOTIFICATION_TYPE.FAIR_DELETE'),
                            'operation_id'      => $fair_id,
                            'notification_at'   => Carbon::now(),
                        ]
                    );
                }
            } else {
                if ($notify_flg)
                {
                    $notificationQueue = NotificationQueue::create(
                        [
                            'notification_id'   => (string)Str::uuid(),
                            'notification_type' => config('const.NOTIFICATION_TYPE.FAIR_MODIFY'),
                            'operation_id'      => $fair_id,
                            'notification_at'   => Carbon::now(),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        // PUTリクエストの更新結果を返却
        $hospitalAppend = HospitalAppend::with([
            "hospital_scholarship",
            "hospital_intership",
            "hospital_practice",
            "hospital_fair"
        ])->findMany($IDs);
        // POSTリクエストの更新結果を返却する
        $fair = Fair::with(["fair_type"])->find($fair_id);
        // FairオブジェクトとHospitalAppendオブジェクトを連結
        $response_data = $fair->setAttribute("append", $hospitalAppend);
        return \Response::json($response_data, Response::HTTP_OK);
    }


    /**
     * 説明会削除
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return void
     */
    public function deleteFairById(FairRequest $request, string $fair_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);

        $fair = Fair::find($fair_id);
        // レコードが見つからない場合
        if (empty($fair)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }

        $response_data = Fair::find($fair_id)->delete();
        if ($response_data !== true) {
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 説明会参加申し込み取得
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return void
     */
    public function getApplicationOfFair(FairRequest $request, string $fair_id)
    {
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);
        $response_data = null;
        // 説明会レコードの存在チェック
        $fair = Fair::find($fair_id);
        if (empty($fair)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }
        $response_data = FairApplication::with(["organization" => function ($query) {
            // 削除済みも取得する
            $query->withTrashed();
        }])
        ->where("fair_id", $fair_id)
        ->withTrashed()
        ->orderBy("created_at", "desc")
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定されたfair_idに紐づく全オンラインイベントを取得する
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return void
     */
    public function getOnlineEventOfFair(FairRequest $request, string $fair_id)
    {
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);
        // 説明会レコードの存在チェック
        $fair = Fair::find($fair_id);
        if (empty($fair)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }
        // 削除済みレコードも取得可能
        $response_data = OnlineEvent::with(["event_member"])
        ->where("fair_id", $fair_id)
        ->withTrashed()
        ->orderBy("created_at", "desc")
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }


    /**
     * 説明情報取得
     *
     * @param FairRequest $request
     * @param string $fair_id
     * @return JsonResponse
     */
    public function getDetailOfFair(FairRequest $request, string $fair_id)
    {
        \Log::debug('['.__FUNCTION__.'] fair_id=' . $fair_id);
        $response_data = null;
        $fair = Fair::find($fair_id);

        // 説明会レコードが存在しない場合
        if (empty($fair)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }
        $fairDetail = FairDetail::with([
            "append_info",
            "append_info.hospital_intership",
            "append_info.hospital_practice",
            "append_info.hospital_scholarship",
            "append_info.hospital_fair",
        ])
        ->where("fair_id", $fair_id)
        ->orderBy("created_at", "desc")
        ->get();
        $response_data = $fairDetail;
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
