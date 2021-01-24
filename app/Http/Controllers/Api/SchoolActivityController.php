<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\SchoolActivityRequest;

class SchoolActivityController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function getSchoolActivity(SchoolActivityRequest $request)
    {
        // レスポンス
        $response_data = null;
        
        // ログ出力
        Log::debug('[学校アクティビティ取得] ' . 'Function: ' . __FUNCTION__);
        
        DB::enableQueryLog();
        
        // 学校取得
        $query = Organization::typeSchool()->with([
            'school',
            'school.fairApplications',
            'eventMembers',
            'eventMembers.onlineEvent'
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
}
