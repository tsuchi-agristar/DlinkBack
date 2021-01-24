<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function createUser(UserRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[ユーザー登録] ' . 'Function: ' . __FUNCTION__ . ' Posted JSON: ' . json_encode($post_json));

        // ユーザー登録
        DB::beginTransaction();
        try {
            $user = new User();
            $user->fill($request->all())
                ->save();
            $user = User::find($user->user_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();

            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // レスポンス
        $response_data = $user;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function updateUserById(UserRequest $request, $user_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[ユーザー更新] ' . 'Function: ' . __FUNCTION__ . ' user_id: ' . $user_id . ', Posted JSON: ' . json_encode($post_json));

        // ユーザー更新
        DB::beginTransaction();
        try {
            $user = User::find($user_id);
            $user->fill($request->all())
                ->save();
            $user = User::find($user->user_id);
        } catch (\Exception $e) {
            Log::critical($e);
            DB::rollback();

            $response_data['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // レスポンス
        $response_data = $user;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    // @formatter:off
    /**
     *
     */
    // @formatter:on
    public function deleteUserById(UserRequest $request, $user_id)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[ユーザー削除] ' . 'Function: ' . __FUNCTION__ . ' user_id: ' . $user_id . ', Posted JSON: ' . json_encode($post_json));

        // ユーザー削除
        DB::beginTransaction();
        try {
            $user = User::findOrFail($user_id);
            $user->delete();
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
    public function login(UserRequest $request)
    {
        // レスポンス
        $response_data = null;

        // ログ出力
        $post_json = $request->json()->all();
        Log::debug('[ログイン] ' . 'Function: ' . __FUNCTION__ . ' Posted JSON: ' . json_encode($post_json));

        DB::enableQueryLog();

        try {
            // ユーザー取得
            $query = User::account($request->account, $request->password)->with('organization');
            // 取得
            $user = $query->firstOrFail();

            if ($user != null && ($user->account_name !== $request->account || $user->password !== $request->password))
            {
                $user = null;
            }
            
        } catch (ModelNotFoundException $e) {
            return \Response::json($response_data, Response::HTTP_OK);
        }

        // レスポンス作成
        $json = $user;

        // レスポンス
        $response_data = $json;
        return \Response::json($response_data, Response::HTTP_OK);
    }
}
