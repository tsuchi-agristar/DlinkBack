<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SchoolRequest;
use App\Models\School;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SchoolController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getSchool(SchoolRequest $request)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        Log::debug('[学校取得] ' . 'Function: ' . __FUNCTION__);
        
        DB::enableQueryLog();
        
        // 学校取得
        $query = Organization::typeSchool()->with([
            'school',
            'user'
        ]);
        
        // // 件数
        // $count = $query->count();
        
        // ソート
        $query->orderBy('Organizations.created_at', 'desc');
        
        // ページ表示数
        $page_size = $request->page_size ?? config('const.DEFAULT_PAGE_SIZE');
        $limit = $page_size;
        
        // ページ番号
        $page_num = $request->page_num ?? config('const.DEFAULT_PAGE_NUM');
        $offset = (($page_num - 1) * $page_size);
        
        // 取得
        $organizations = $query->skip($offset)
            ->take($limit)
            ->get();
        // dd(DB::getQueryLog());
        // dd($organizations);
        
        // レスポンス作成
        $json = $organizations;
        
        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function addSchool(SchoolRequest $request)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[学校登録] ' . 'Function: ' . __FUNCTION__ . ' Posted JSON: ' . json_encode($post_json));
        
        // 学校登録
        DB::beginTransaction();
        try {
            $school = new School();
            $school->fill($request->all())
                ->save();
            $school = School::find($school->school_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();
            
            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        
        // レスポンス
        $response_data = $school;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getSchoolById(SchoolRequest $request, $school_id)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[学校取得] ' . 'Function: ' . __FUNCTION__ . ' school_id: ' . $school_id . ' Posted JSON: ' . json_encode($post_json));
        
        DB::enableQueryLog();
        
        try {
            // 学校取得
            $school = School::findOrFail($school_id);
        } catch (ModelNotFoundException $e) {
            // $response_data['error'][] = $e->getMessage();
            // return \Response::json($response_data, Response::HTTP_UNPROCESSABLE_ENTITY);
            return \Response::json([], Response::HTTP_OK);
        }
        
        // レスポンス作成
        $json = $school;
        
        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function updateSchoolById(SchoolRequest $request, $school_id)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[学校更新] ' . 'Function: ' . __FUNCTION__ . ' school_id: ' . $school_id . ', Posted JSON: ' . json_encode($post_json));
        
        // 学校更新
        DB::beginTransaction();
        try {
            $school = School::find($school_id);
            $school->fill($request->all())
                ->save();
            $school = School::find($school->school_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();
            
            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();
        
        // レスポンス
        $response_data = $school;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function deleteSchoolById(SchoolRequest $request, $school_id)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[学校削除] ' . 'Function: ' . __FUNCTION__ . ' school_id: ' . $school_id . ', Posted JSON: ' . json_encode($post_json));
        
        $school = School::find($school_id);
        if (empty($school)) {
            return \Response::json($response_data, Response::HTTP_OK);
        }
        // ユーザー削除
        DB::beginTransaction();
        try {
            $school->delete();
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
