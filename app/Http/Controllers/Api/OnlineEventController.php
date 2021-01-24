<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OnlineEventRequest;
use App\Models\OnlineEvent;
use App\Models\Estimate;
use App\Models\NotificationQueue;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OnlineEventController extends Controller
{
    /**
     * オンラインイベント情報の取得
     *
     * @param OnlineEventRequest $request
     * @return void
     */
    public function getOnlineEvent(OnlineEventRequest $request)
    {
        $response_data = null;
        $response_data = OnlineEvent::with([
            "fair",
            "fair.fair_type",
            "fair.organization" => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            "event_member",
            "event_member.organization" => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            "estimate"
        ])
        ->where('event_status', Config("const.EVENT_STATUS.OFFICIAL"))
        ->orderBy("created_at", "desc")
        ->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * オンラインイベント情報の登録処理
     *
     * @param OnlineEventRequest $request
     * @return void
     */
    public function createOnlineEvent(OnlineEventRequest $request)
    {
        $response_data = null;
        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));
        DB::beginTransaction();

        try {
            $online_event = OnlineEvent::create($request->all());

            //通知登録
            if ($online_event->event_status == Config("const.EVENT_STATUS.TENTATIVE")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_TENTATIVE'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($online_event->event_status == Config("const.EVENT_STATUS.OFFICIAL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_OFFICIAL'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($online_event->event_status == Config("const.EVENT_STATUS.CANCEL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_CANCEL'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data["data"]["error"][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        DB::commit();
        $response_data = $online_event;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したevent_idに紐づくオンラインイベント情報を取得(削除済みも参照可能)
     *
     * @param OnlineEventRequest $request
     * @param string $event_id
     * @return void
     */
    public function getOnlineEventById(OnlineEventRequest $request, string $event_id)
    {
        $response_data = null;
        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] event_id=' . $event_id);

        $data = OnlineEvent::withTrashed()->with(['estimate'])->find($event_id);

        if (empty($data)) {
            // レコードが見つからない場合
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }

        $response_data = $data;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したevent_idに紐づくオンラインイベント情報を更新する
     *
     * @param OnlineEventRequest $request
     * @param string $event_id
     * @return void
     */
    public function updateOnlineEventById(OnlineEventRequest $request, string $event_id)
    {
        $response_data = null;
        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] event_id=' . $event_id);
        \Log::debug('['.__FUNCTION__.'] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        $online_event = OnlineEvent::find($event_id);

        //通知フラグ(ステータスが更新される場合にtrue)
        $request_event_status = $request->event_status;
        $notify_flg = isset($request_event_status) ? $request_event_status != $online_event->event_status : false;

        // スタータス整合性確認(キャンセル/完了/完了(未請求)からは変更不可)
        if ($online_event->event_status == Config("const.EVENT_STATUS.CANCEL")
         || $online_event->event_status == Config("const.EVENT_STATUS.DONE")
         || $online_event->event_status == Config("const.EVENT_STATUS.DONE_NO_PAY"))
        {
            //ステータスが異なる場合のみエラーとする
            if (isset($request_event_status) && $request_event_status != $online_event->event_status) {
                $response_data['message'] = 'The status cannot be updated.';
                $response_data['errors'] = 'event_status';
                return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        try {
            //正式決定後にイベント状態(キャンセル/完了/完了(未請求))のみ変更が可能、他の変更は不可
            if ($online_event->event_status == Config("const.EVENT_STATUS.OFFICIAL"))
            {
                if (isset($request->event_status) &&
                  (  $request->event_status == Config("const.EVENT_STATUS.CANCEL")
                  || $request->event_status == Config("const.EVENT_STATUS.DONE")
                  || $request->event_status == Config("const.EVENT_STATUS.DONE_NO_PAY")))
                {
                    $online_event->event_status = $request->event_status;
                    $online_event->save();
                } else {
                    $request_event_status = $request->event_status;
                    //ステータスが異なる場合のみエラーとする
                    if (isset($request_event_status) && $request_event_status != $online_event->event_status) {
                        $response_data['message'] = 'The status cannot be updated.';
                        $response_data['errors'] = 'event_status';
                        return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
                    } else {
                        //イベント種別が個別の場合(個別チャンネル登録)は、ステータス以外も更新可能とする
                        if ($online_event->event_type == config('const.EVENT_TYPE.INDIVIDUAL'))
                        {
                            $online_event->fill($request->all())->save();
                        }
                    }
                }
            } else {
                $online_event->fill($request->all())->save();
            }

            //通知登録
            //イベント種別が個別の場合(個別チャンネル登録)
            if ($online_event->event_type == config('const.EVENT_TYPE.INDIVIDUAL')) {
                if ($online_event->event_status == Config("const.EVENT_STATUS.OFFICIAL")) {
                    $notificationQueue = NotificationQueue::create(
                        [
                            'notification_id'   => (string)Str::uuid(),
                            'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_OFFICIAL'),
                            'operation_id'      => $online_event->event_id,
                            'notification_at'   => Carbon::now(),
                        ]
                    );
                }
            } else if ($notify_flg && $online_event->event_status == Config("const.EVENT_STATUS.TENTATIVE")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_TENTATIVE'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($notify_flg && $online_event->event_status == Config("const.EVENT_STATUS.OFFICIAL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_OFFICIAL'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($notify_flg && $online_event->event_status == Config("const.EVENT_STATUS.CANCEL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.ONLINE_EVENT_CANCEL'),
                        'operation_id'      => $online_event->event_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data["data"]["error"][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        $response_data = $online_event;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定したevent_idに紐づくオンラインイベントを削除する
     *
     * @param OnlineEventRequest $request
     * @param string $event_id
     * @return void
     */
    public function deleteOnlineEventById(OnlineEventRequest $request, string $event_id)
    {
        $response_data = null;
        // 規定ログ
        \Log::debug('['.__FUNCTION__.'] event_id=' . $event_id);
        $data = OnlineEvent::find($event_id);

        if (empty($data)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }
        // レコードが存在
        $result = $data->delete();
        $response_data = $result;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 指定した event_idのオンラインイベントの見積もり取得
     *
     * @param OnlineEventRequest $request
     * @param string $event_id
     * @return void
     */
    public function getEstimateOfEvent(OnlineEventRequest $request, string $event_id)
    {
        $response_data = null;
        \Log::debug('['.__FUNCTION__.'] event_id=' . $event_id);
        $data = Estimate::withTrashed()->where("event_id", $event_id)->first();

        // レコードが取得できない場合
        if (empty($data)) {
            $response_data = null;
            return \Response::json($response_data, Response::HTTP_OK);
        }
        $response_data = $data;
        return \Response::json($response_data, Response::HTTP_OK);
    }


}
