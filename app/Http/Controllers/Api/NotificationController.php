<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Models\NotificationQueue;
use App\Models\NotificationDestination;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * 通知登録
     *
     * @param NotificationRequest $request
     * @return void
     */
    public function createNotification(NotificationRequest $request)
    {
        $response_data = null;
        \Log::debug('[通知登録] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            $notificationQueue = NotificationQueue::create(
                [
                    'notification_id' => $request->notification_id,
                    'notification_type' => $request->notification_type,
                    'operation_id' => $request->operation_id,
                    'notification_at' => Carbon::now(),
                ]
            );
            $response_data = $notificationQueue;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 通知一覧取得
     *
     * @param NotificationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function getNotificationsByOrganizationId(NotificationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[通知一覧取得] organization_id: ' . $organization_id);
        $model = NotificationDestination::notificationList($organization_id)->get();
        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 通知取得
     *
     * @param NotificationRequest $request
     * @param string $organization_id
     * @param string $notification_id
     * @return void
     */
    public function getNotification(NotificationRequest $request, $organization_id, $notification_id)
    {
        $response_data = null;
        \Log::debug('[通知取得] organization_id: ' . $organization_id . ' notification_id: ' . $notification_id);
        $model = NotificationDestination::notificationList($organization_id)
            ->where('notifications.notification_id', '=', $notification_id)
            ->first();
        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 通知既読更新
     *
     * @param NotificationRequest $request
     * @return void
     */
    public function updateNotification(NotificationRequest $request)
    {
        $response_data = null;
        \Log::debug('[通知既読更新] request=' . json_encode($request->json()->all()));

        $notification = NotificationDestination::
            where('organization_id', '=', $request->organization_id)
            ->where('notification_id', '=', $request->notification_id)
            ->first();
        DB::beginTransaction();
        try {
            if ($notification)
            {
                $notification->confirm_status = config('const.NOTIFICATION_STATUS')['READED'];
                $notification->save();
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = $notification;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 通知一括既読更新
     *
     * @param NotificationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function updateNotificationOfOrganization(NotificationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[通知一括既読更新] organization_id: ' . $organization_id);

        DB::beginTransaction();
        try {
            NotificationDestination::where('organization_id', '=', $organization_id)
            ->update(['confirm_status' => config('const.NOTIFICATION_STATUS.READED')]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = true;
        return \Response::json($response_data, Response::HTTP_OK);
    }
}
