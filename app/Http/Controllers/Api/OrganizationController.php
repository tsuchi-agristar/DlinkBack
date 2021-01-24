<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Models\School;
use App\Models\Hospital;
use App\Models\User;
use App\Models\NotificationQueue;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * 全組織取得
     *
     * @param HospitalRequest $request
     * @return void
     */
    public function getOrganization(OrganizationRequest $request)
    {
        $response_data = null;
        \Log::debug('[getOrganization] ' . 'Function: ' . __FUNCTION__);
        $model = Organization::
            orderBy("organization_type", "desc")
            ->orderBy("created_at", "desc")
            ->get();
        $response_data = $model;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 組織登録
     *
     * @param OrganizationRequest $request
     * @return void
     */
    public function addOrganization(OrganizationRequest $request)
    {
        $response_data = null;
        \Log::debug('[addOrganization] request=' . json_encode($request->json()->all()));

        $organization = $request->organization;
        $organization_sub = $request->organization_sub;
        $user = $request->user;
        $organization_type = $organization["organization_type"];

        DB::beginTransaction();
        try {
            $neworganization = Organization::create($organization);
            $response_data['organization'] = $neworganization;

            if ($organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL')) {
                $newhospital = Hospital::create($organization_sub);
                $response_data['hospital'] = $newhospital;
            }
            else if ($organization_type == config('const.ORGANIZATION_TYPE.SCHOOL')) {
                $newschool = School::create($organization_sub);
                $response_data['school'] = $newschool;
            }

            $newuser = User::create($user);
            $response_data['user'] = $newuser;

             //通知登録
             $notificationQueue = NotificationQueue::create(
                [
                    'notification_id'   => (string)Str::uuid(),
                    'notification_type' => config('const.NOTIFICATION_TYPE.ORGANIZATION_REGISTER'),
                    'operation_id'      => $neworganization->organization_id,
                    'notification_at'   => Carbon::now(),
                ]
            );
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data = null;
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 組織取得
     *
     * @param OrganizationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function getOrganizationById(OrganizationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[getOrganizationById] organization_id=' . $organization_id);

        $response_data = Organization::withTrashed()->find($organization_id);
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 組織更新
     *
     * @param OrganizationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function updateOrganizationById(OrganizationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[updateOrganizationById] organization_id=' . $organization_id . ' request=' . json_encode($request->json()->all()));

        $organization = Organization::find($organization_id);
        DB::beginTransaction();
        try {
            $organization->fill($request->all())->save();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
            $response_data['data']['error'][] = $e->getMessage();
            return \Response::json($response_data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        DB::commit();

        $response_data = $organization;
        return \Response::json($response_data, Response::HTTP_OK);
    }

    /**
     * 組織削除
     *
     * @param OrganizationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function deleteOrganizationById(OrganizationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[deleteOrganizationById] organization_id=' . $organization_id);

        $organization = Organization::find($organization_id);
        DB::beginTransaction();
        try {
            $data = $organization->delete();
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
     * ユーザー取得
     * ※組織IDからユーザー情報取得
     *
     * @param OrganizationRequest $request
     * @param string $organization_id
     * @return void
     */
    public function getOrganizationUserById(OrganizationRequest $request, $organization_id)
    {
        $response_data = null;
        \Log::debug('[getOrganizationUserById] organization_id=' . $organization_id);

        $users = Organization::with('users')
            ->withTrashed()
            ->find($organization_id)
            ->users;

        $response_data =  $users->first(); // 将来的には１組織複数ユーザーになるかも。現状は１組織に１ユーザー。
        return \Response::json($response_data, Response::HTTP_OK);
    }

}
