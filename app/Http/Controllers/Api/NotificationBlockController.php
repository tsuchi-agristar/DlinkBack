<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationBlockRequest;
use App\Models\NotificationBlock;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NotificationBlockController extends Controller
{
    /**
     * 通知ブロック登録
     *
     * @param NotificationBlockRequest $request
     * @return void
     */
    public function createNotificationblock(NotificationBlockRequest $request)
    {
        $response_data = null;
        \Log::debug('[createNotificationblock] request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            $data = NotificationBlock::create($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = $data;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 通知ブロック削除
     *
     * @param NotificationBlockRequest $request
     * @param string $organization_id
     * @return void
     */
    public function deleteNotificationblockById(NotificationBlockRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[deleteNotificationblockById] organization_id=' . $organization_id);

        $notificationBlock = NotificationBlock::find($organization_id);
        DB::beginTransaction();
        try {
            $data = $notificationBlock->delete();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = $data;
        return \Response::json($response_data, Response::HTTP_OK);
    }

}
