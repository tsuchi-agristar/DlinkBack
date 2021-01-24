<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\QuestionaryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Questionary;
use App\Models\NotificationQueue;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QuestionaryController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function createQuestionary(QuestionaryRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[説明会アンケート登録] ' . 'Function: ' . __FUNCTION__ . ', Posted JSON: ' . json_encode($post_json));

        // 説明会アンケート登録
        DB::beginTransaction();
        try {
            $questionary = new Questionary();
            $questionary->fill($request->all())
                ->save();

            $questionary->questionary_fair_types()->createMany($request->get('questionaryFairType', []));
            $questionary->questionary_hospitals()->createMany($request->get('questionaryHospital', []));
            $questionary->questionary_places()->createMany($request->get('questionaryPlace', []));
            $questionary->questionary_hospital_types()->createMany($request->get('questionaryHospitalType', []));

            $questionary = Questionary::find($questionary->questionary_id);

            //通知登録
            $notificationQueue = NotificationQueue::create(
                [
                    'notification_id'   => (string)Str::uuid(),
                    'notification_type' => config('const.NOTIFICATION_TYPE.QUESTIONARY_REGISTER'),
                    'operation_id'      => $questionary->questionary_id,
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
        $response_data = $questionary;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getQuestionary(QuestionaryRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        Log::debug('[説明会アンケート取得] ' . 'Function: ' . __FUNCTION__);

        DB::enableQueryLog();

        // 説明会アンケート取得
        $query = Questionary::withoutTrashed()->with([
            'questionary_fair_types',
            'questionary_hospitals',
            'questionary_hospitals.organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            'questionary_places',
            'questionary_hospital_types',
            'organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            }
        ]);

        // // 件数
        // $count = $query->count();

        // ソート
        $query->orderBy('Questionary.created_at', 'desc');

        // ページ表示数
        $page_size = $request->page_size ?? config('const.DEFAULT_PAGE_SIZE');
        $limit = $page_size;

        // ページ番号
        $page_num = $request->page_num ?? config('const.DEFAULT_PAGE_NUM');
        $offset = (($page_num - 1) * $page_size);

        // 取得
        $questionaries = $query->skip($offset)
            ->take($limit)
            ->get();
        // dd(DB::getQueryLog());
        // dd($organizations);

        // レスポンス作成
        $json = $questionaries;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }
}
