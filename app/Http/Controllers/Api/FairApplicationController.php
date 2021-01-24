<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\FairApplicationRequest;
use App\Models\FairApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\NotificationQueue;

class FairApplicationController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getFairApplication(FairApplicationRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        Log::debug('[説明会申込取得] ' . 'Function: ' . __FUNCTION__);

        DB::enableQueryLog();

        // 説明会申込取得
        $query = FairApplication::search($request)->with([
            'organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            'fair',
            'fair.fair_type',
            'fair.organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            'fair.online_events'
        ]);

        // // 件数
        // $count = $query->count();

        // ソート
        $query->orderBy('FairApplications.created_at', 'desc');

        // ページ表示数
        $page_size = $request->page_size ?? config('const.DEFAULT_PAGE_SIZE');
        $limit = $page_size;

        // ページ番号
        $page_num = $request->page_num ?? config('const.DEFAULT_PAGE_NUM');
        $offset = (($page_num - 1) * $page_size);

        // 取得
        $fairApplications = $query->skip($offset)
            ->take($limit)
            ->get();
        // dd(DB::getQueryLog());
        // dd($organizations);

        // レスポンス作成
        $json = $fairApplications;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function createFairApplication(FairApplicationRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込登録] ' . 'Function: ' . __FUNCTION__ . ' Posted JSON: ' . json_encode($post_json));

        // 説明会申込登録
        DB::beginTransaction();
        try {
            $fairApplication = new FairApplication();
            $fairApplication->fill($request->all())
                ->save();
            $fairApplication = FairApplication::find($fairApplication->application_id);

            //通知登録
            $notificationQueue = NotificationQueue::create(
                [
                    'notification_id'   => (string)Str::uuid(),
                    'notification_type' => config('const.NOTIFICATION_TYPE.APPLICATION_REGISTER'),
                    'operation_id'      => $fairApplication->application_id,
                    'notification_at'   => Carbon::now(),
                ]
            );
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();

            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // レスポンス
        $response_data = $fairApplication;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getFairApplicationById(FairApplicationRequest $request, $application_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込取得] ' . 'Function: ' . __FUNCTION__ . ' application_id: ' . $application_id . ' Posted JSON: ' . json_encode($post_json));

        DB::enableQueryLog();

        try {
            // 説明会申込取得
            $fairApplication = FairApplication::withTrashed()->with([
                'organization'
            ])->findOrFail($application_id);
        } catch (ModelNotFoundException $e) {
            // $response_data['error'][] = $e->getMessage();
            // return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            return \Response::json([], Response::HTTP_OK);
        }

        // レスポンス作成
        $json = $fairApplication;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function updateFairApplicationById(FairApplicationRequest $request, $application_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込更新] ' . 'Function: ' . __FUNCTION__ . ' application_id: ' . $application_id . ', Posted JSON: ' . json_encode($post_json));

        // 説明会申込更新
        DB::beginTransaction();
        try {
            $fairApplication = FairApplication::find($application_id);
            $fairApplication->fill($request->all())
                ->save();
            $fairApplication = FairApplication::find($fairApplication->application_id);

            //通知登録
            if ($fairApplication->application_status == Config("const.APPLICATION_STATUS.APPLYING")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.APPLICATION_REGISTER'),
                        'operation_id'      => $fairApplication->application_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($fairApplication->application_status == Config("const.APPLICATION_STATUS.CANCEL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.APPLICATION_CANCEL'),
                        'operation_id'      => $fairApplication->application_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            } else if ($fairApplication->application_status == Config("const.APPLICATION_STATUS.WITHDRAW")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.APPLICATION_WITHDRAW'),
                        'operation_id'      => $fairApplication->application_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();

            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // レスポンス
        $response_data = $fairApplication;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getFairOfApplication(FairApplicationRequest $request, $application_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込取得] ' . 'Function: ' . __FUNCTION__ . ' application_id: ' . $application_id . ' Posted JSON: ' . json_encode($post_json));

        DB::enableQueryLog();

        try {
            // 説明会申込取得
            $fairApplication = FairApplication::with([
                'fair',
                'fair.fair_type',
                'fair.organization' => function ($query) {
                    // 削除済みも取得する
                    $query->withTrashed();
                },
                'fair.online_events_latest'
            ])->findOrFail($application_id);
        } catch (ModelNotFoundException $e) {
            // $response_data['error'][] = $e->getMessage();
            // return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            return \Response::json([], Response::HTTP_OK);
        }

        // レスポンス作成
        $json = $fairApplication;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function deleteFairApplication(FairApplicationRequest $request, $application_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込削除] ' . 'Function: ' . __FUNCTION__ . ' application_id: ' . $application_id . ', Posted JSON: ' . json_encode($post_json));

        // ユーザー削除
        DB::beginTransaction();
        try {
            $fairApplication = FairApplication::findOrFail($application_id);
            $fairApplication->delete();
        } catch (ModelNotFoundException $e) {
            return \Response::json([], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();

            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // レスポンス
        $response_data = [
            true
        ];
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getApplicationOfFairApplication(FairApplicationRequest $request, $fair_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会申込取得] ' . 'Function: ' . __FUNCTION__ . ' fair_id: ' . $fair_id . ' Posted JSON: ' . json_encode($post_json));

        DB::enableQueryLog();

        try {
            // 説明会申込取得
            $fairApplication = FairApplication::
                where('fair_id', $fair_id)
                ->where('application_status', '!=', config('const.APPLICATION_STATUS')['CANCEL'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (ModelNotFoundException $e) {
            // $response_data['error'][] = $e->getMessage();
            // return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            return \Response::json([], Response::HTTP_OK);
        }

        // レスポンス作成
        $json = $fairApplication;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }
}
