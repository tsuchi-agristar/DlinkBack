<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\NotificationQueue;

class PaymentController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getPayment(PaymentRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        Log::debug('[請求一覧取得] ' . 'Function: ' . __FUNCTION__);

        DB::enableQueryLog();

        // 請求一覧取得
        $query = Payment::withoutTrashed()->with([
            'organization' => function ($query) {
                $query->withTrashed();
            }
        ]);

        // // 件数
        // $count = $query->count();

        // ソート
        $query->orderBy('Payments.created_at', 'desc');

        // ページ表示数
        $page_size = $request->page_size ?? config('const.DEFAULT_PAGE_SIZE');
        $limit = $page_size;

        // ページ番号
        $page_num = $request->page_num ?? config('const.DEFAULT_PAGE_NUM');
        $offset = (($page_num - 1) * $page_size);

        // 取得
        $payments = $query->skip($offset)
            ->take($limit)
            ->get();
        // dd(DB::getQueryLog());
        // dd($organizations);

        // レスポンス作成
        $json = $payments;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getpaymentById(PaymentRequest $request, $payment_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[請求取得] ' . 'Function: ' . __FUNCTION__ . ' payment_id: ' . $payment_id . ' Posted JSON: ' . json_encode($post_json));

        DB::enableQueryLog();

        try {
            // 請求取得
            $payment = Payment::with([
                'organization' => function ($query) {
                    $query->withTrashed();
                },
                'payment_details',
                'payment_details.estimate',
                'payment_details.estimate.online_event',
                'payment_details.estimate.online_event.fair',
                'payment_details.estimate.online_event.fair.fair_type',
                'payment_details.estimate.online_event.event_member',
                'payment_details.estimate.online_event.event_member.organization' => function ($query) {
                    $query->withTrashed();
                }
            ])->findOrFail($payment_id);
        } catch (ModelNotFoundException $e) {
            // $response_data['error'][] = $e->getMessage();
            // return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            return \Response::json([], Response::HTTP_OK);
        }

        // レスポンス作成
        $json = $payment;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function updatePaymentById(PaymentRequest $request, $payment_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        \Log::debug('[請求更新] ' . 'Function: ' . __FUNCTION__ . ' payment_id: ' . $payment_id . ' Posted JSON: ' . json_encode($post_json));

        $payment = Payment::find($payment_id);

        //通知フラグ(ステータスが更新される場合にtrue)
        $request_payment_status = $request->payment_status;
        $notify_flg = isset($request_payment_status) ? $request_payment_status != $payment->payment_status : false;

        // スタータス整合性確認(最終決定済からは変更不可)
        if ($payment->payment_status == Config("const.PAYMENT_STATUS.OFFICIAL"))
        {
            //ステータスが異なる場合のみエラーとする
            if (isset($request_payment_status) && $request_payment_status != $payment->payment_status) {
                $response_data['message'] = 'The status cannot be updated.';
                $response_data['errors'] = 'payment_status';
                return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::beginTransaction();
        try {
            $payment->fill($request->all())->save();

            //通知登録
            if ($notify_flg && $payment->payment_status == Config("const.PAYMENT_STATUS.OFFICIAL")) {
                $notificationQueue = NotificationQueue::create(
                    [
                        'notification_id'   => (string)Str::uuid(),
                        'notification_type' => config('const.NOTIFICATION_TYPE.PAYMENT_REGISTER'),
                        'operation_id'      => $payment->payment_id,
                        'notification_at'   => Carbon::now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = $payment;
        return \Response::json($response_data, Response::HTTP_OK);
    }

}
