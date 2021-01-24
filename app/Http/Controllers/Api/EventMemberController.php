<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EventMemberRequest;
use App\Models\EventMember;

class EventMemberController extends Controller
{
    public function getChannelbyMemberId(EventMemberRequest $request, $organization_id)
    {
        $response_data = null;
        // ログ出力
        \Log::debug('[getChannelbyMemberId] organization_id=' . $organization_id);

        // イベントメンバー取得
        $query = EventMember::where('organization_id', $organization_id);
        $query = $query->with([
            'organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            'onlineEvent',
            'onlineEvent.fair',
            'onlineEvent.fair.fair_type',
            'onlineEvent.fair.organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
            'onlineEvent.event_member',
        ]);
        $response_data = $query->get();

        return \Response::json($response_data, Response::HTTP_OK);
    }

    public function getEventMemberById(EventMemberRequest $request, $event_id)
    {
        $response_data = null;
        // ログ出力
        \Log::debug('[getEventMemberById] event_id=' . $event_id);

        // イベントメンバー取得
        $query = EventMember::where('event_id', $event_id);
        $query = $query->with([
            'organization' => function ($query) {
                // 削除済みも取得する
                $query->withTrashed();
            },
        ]);
        $response_data = $query->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }

    public function updateEventMember(EventMemberRequest $request, $event_id)
    {
        $response_data = null;
        // ログ出力
        \Log::debug('[updateEventMember] event_id=' . $event_id . ', request=' . json_encode($request->json()->all()));

        DB::beginTransaction();
        try {
            //イベントメンバーテーブルより、パラメータのイベントIDである行(複数)を物理削除する
            EventMember::where('event_id', $event_id)->delete();

            //イベントメンバーテーブルへイベントメンバー(複数)を、パラメータの配列数だけ登録する
            foreach ($request->all() as $index => $value) {
                $value["event_id"] = $event_id;
                EventMember::create($value);
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        // イベントメンバー取得
        $response_data = EventMember::where('event_id', $event_id)->get();
        return \Response::json($response_data, Response::HTTP_OK);
    }

}
