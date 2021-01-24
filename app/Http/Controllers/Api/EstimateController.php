<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\EstimateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Estimate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EstimateController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function createEstimate(EstimateRequest $request)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[見積登録] ' . 'Function: ' . __FUNCTION__ . ' Posted JSON: ' . json_encode($post_json));
        
        // 見積登録
        DB::beginTransaction();
        try {
            $estimate = new Estimate();
            $estimate->fill($request->all())
                ->save();
            $estimate = Estimate::find($estimate->estimate_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();
            
            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        
        // レスポンス
        $response_data = $estimate;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function updateEstimateById(EstimateRequest $request, $estimate_id)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[見積更新] ' . 'Function: ' . __FUNCTION__ . 'estimate_id: ' . $estimate_id . ', Posted JSON: ' . json_encode($post_json));
        
        // 見積更新
        DB::beginTransaction();
        try {
            $estimate = Estimate::find($estimate_id);

            // スタータス整合性確認(最終決定済からは変更不可)
            if ($estimate->estimate_status == Config("const.ESTIMATE_STATUS.OFFICIAL"))
            {
                $request_estimate_status = $request->estimate_status;
                //ステータスが異なる場合のみエラーとする
                if (isset($request_estimate_status) && $request_estimate_status != $estimate->estimate_status) {
                    $response_data['message'] = 'The status cannot be updated.';
                    $response_data['errors'] = 'estimate_status';
                    return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $estimate->fill($request->all())
                ->save();
            $estimate = Estimate::find($estimate->estimate_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();
            
            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        
        // レスポンス
        $response_data = $estimate;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function deleteEstimateById(EstimateRequest $request, $estimate_id)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[見積削除] ' . 'Function: ' . __FUNCTION__ . 'estimate_id: ' . $estimate_id . ', Posted JSON: ' . json_encode($post_json));
        
        // ユーザー削除
        DB::beginTransaction();
        try {
            $estimate = Estimate::findOrFail($estimate_id);
            $estimate->delete();
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
}
