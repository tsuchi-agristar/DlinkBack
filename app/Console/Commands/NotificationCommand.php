<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\NotificationQueue;
use App\Models\NotificationDestination;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\Fair;
use App\Models\FairApplication;
use App\Models\OnlineEvent;
use App\Models\Questionary;
use App\Models\Payment;
use App\Models\NotificationBlock;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification Batch';

    private $format = 'Y-m-d H:i:s.v';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('[NotificationCommand Start] ' . __METHOD__ . '::' . (__LINE__) );

        //通知キュー取得
        $queues = NotificationQueue::where('notification_at', '<=', Carbon::parse('-1 minute'))->orderBy("notification_at", "asc")->get();

        foreach ($queues as $queue)
        {
            \Log::info('Queues: ' . $queue);
            DB::beginTransaction();
            try {
                $notification_id = $queue->notification_id;
                $notification_type = $queue->notification_type;
                $operation_id = $queue->operation_id;
                $notification_at = $queue->notification_at;
    
                switch($notification_type) {
                    case config('const.NOTIFICATION_TYPE.ORGANIZATION_REGISTER'): // 新規登録
                        $this->notifyOrganization($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.FAIR_REGISTER'): // オンライン説明会登録
                    case config('const.NOTIFICATION_TYPE.FAIR_MODIFY'): // オンライン説明会変更
                    case config('const.NOTIFICATION_TYPE.FAIR_DELETE'): // オンライン説明会削除
                        $this->notifyFair($notification_id, $notification_type, $operation_id, $notification_at, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.APPLICATION_REGISTER'): // オンライン説明会申込
                    case config('const.NOTIFICATION_TYPE.APPLICATION_CANCEL'): // オンライン説明会申込キャンセル
                    case config('const.NOTIFICATION_TYPE.APPLICATION_WITHDRAW'): // オンライン説明会申込取下げ
                        $this->notifyApplication($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.ONLINE_EVENT_TENTATIVE'): // オンライン説明会仮決定
                        $this->notifyOnlineEventTentative($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.ONLINE_EVENT_OFFICIAL'): // オンライン説明会正式決定
                        $this->notifyOnlineEventOfficial($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.ONLINE_EVENT_CANCEL'): // オンライン説明会キャンセル
                        $this->notifyOnlineEventCancel($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.QUESTIONARY_REGISTER'): // アンケート登録
                        $this->notifyQuestionary($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    case config('const.NOTIFICATION_TYPE.PAYMENT_REGISTER'): // 請求情報通知
                        $this->notifyPayment($notification_id, $notification_type, $operation_id, $notification_at);
                        break;
                    default:
                        break;
                }
                $queue->delete();
            } catch (\Exception $e) {
                DB::rollback();
                \Log::critical($e);
            }
            DB::commit();
        }

        \Log::info('[NotificationCommand End] ' . __METHOD__ . '::' . (__LINE__) );
        return true;
    }

    
    /**
     * 組織(学校/病院)新規登録
     * @param String $notification_id
     * @param String $notification_type
     * @param String $organization_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyOrganization($notification_id, $notification_type, $organization_id, $notification_at)
    {
        //組織テーブルから、組織を取得する
        $newOrganization = Organization::find($organization_id);

        //タイトルとメッセージの作成
        $title = '';
        $content = '';

        //組織が削除済の場合、通知作成せずに終了
        if (empty($newOrganization)) {
            return;
        }
        
        //組織タイプ
        $organization_type = $newOrganization->organization_type;
        //病院
        if ($organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL')) 
        {
            $title = "【病院新規登録】" . $newOrganization->organization_name;
            $content = "下記の病院情報が新規登録されました。"
                     . "\n"
                     . "\n　病院名　　　　：" . $newOrganization->organization_name . " (" . $newOrganization->prefecture . $newOrganization->city . ")"
                     . "\n";
        } 
        //学校
        else 
        {
            $title = "【学校新規登録】" . $newOrganization->organization_name;
            $content = "下記の学校情報が新規登録されました。"
                     . "\n"
                     . "\n　学校名　　　　：" . $newOrganization->organization_name . " (" . $newOrganization->prefecture . $newOrganization->city . ")"
                     . "\n";
        }

        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        //ダミー病院/学校
        if ($newOrganization->dummy)
        {
            $query->where('organization_id', $newOrganization->organization_id) //自身の組織
                  ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        //病院
        else if ($organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL')) 
        {
            $query->where('organization_id', $newOrganization->organization_id) //自身の組織
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.SCHOOL')) //学校組織すべて
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        //学校
        else
        {
            $query->where('organization_id', $newOrganization->organization_id) //自身の組織
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.HOSPITAL')) //病院組織すべて
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }


    /**
     * オンライン説明会登録/変更/削除
     * @param String $notification_id
     * @param String $notification_type
     * @param String $fair_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyFair($notification_id, $notification_type, $fair_id, $notification_at)
    {
        //説明会テーブルから、説明会を取得する
        $fair = Fair::withTrashed()->with(["fair_type", "organization" => function($q){$q->withTrashed();}])->find($fair_id);

        //組織テーブルから、開催病院組織を取得する
        $theHospital = $fair->organization;
        
        //タイトルとメッセージの作成
        $title = '';
        $content = '';

        switch($notification_type) {
            case config('const.NOTIFICATION_TYPE.FAIR_REGISTER'): // オンライン説明会登録
                $title = '【オンライン説明会情報登録】';
                $content = '下記のオンライン説明会情報が登録されました。';
                break;
            case config('const.NOTIFICATION_TYPE.FAIR_MODIFY'): // オンライン説明会変更
                $title = '【オンライン説明会情報変更】';
                $content = '下記のオンライン説明会情報が更新されました。';
                break;
            case config('const.NOTIFICATION_TYPE.FAIR_DELETE'): // オンライン説明会削除
                $title = '【オンライン説明会情報削除】';
                $content = '下記のオンライン説明会情報がキャンセルされました。';
                break;
            default:
                break;
        }

        $title .= $theHospital->organization_name . " / 病院説明会(募集期間：";
        $title .= $fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定";
        $title .= "-";
        $title .= $fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定";
        $title .= ")";

        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        $appendInfoTypes = array_column(json_decode(json_encode($fair->fair_type), true), "fair_type");

        $content .= "\n";
        $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
        $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
        $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
        $content .= "\n　募集期間　　　：" . ($fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定") . "～" . ($fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定");
        $content .= "\n";
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得(メモ:通知組織には開催病院自身も含む)
        $query = Organization::query();
        //開催病院組織がダミー
        if ($theHospital->dummy)
        {
            $query->where('organization_id', $theHospital->organization_id) //自身の組織
                  ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        else
        {
            $query->where('organization_id', $theHospital->organization_id) //自身の組織
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.SCHOOL')) //学校組織すべて
                   ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }


    /**
     * オンライン説明会申込/オンライン説明会申込キャンセル/オンライン説明会申込取下げ
     * @param String $notification_id
     * @param String $notification_type
     * @param String $application_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyApplication($notification_id, $notification_type, $application_id, $notification_at)
    {
        //説明会参加申込テーブルから、説明会参加申込を取得する
        $fairApplication = FairApplication::withTrashed()->with(["organization" => function($q){$q->withTrashed();}])->find($application_id);

        //説明会テーブルから、説明会を取得する
        $fair = Fair::withTrashed()->with(["fair_type", "organization" => function($q){$q->withTrashed();}])->find($fairApplication->fair_id);

        //組織テーブルから、開催病院組織を取得する
        $theHospital = $fair->organization;

        //組織テーブルから、申込学校組織を取得する
        $theSchool = $fairApplication->organization;

        //タイトルとメッセージの作成
        $title = '';
        $content = '';

        switch($notification_type) {
            case config('const.NOTIFICATION_TYPE.APPLICATION_REGISTER'): // オンライン説明会申込
                $title = '【オンライン説明会申込】';
                $content = '下記のオンライン説明会に申込されました。';
                break;
            case config('const.NOTIFICATION_TYPE.APPLICATION_CANCEL'): // オンライン説明会申込キャンセル
                $title = '【オンライン説明会申込キャンセル】';
                $content = '下記のオンライン説明会申込をキャンセルしました。';
                break;
            case config('const.NOTIFICATION_TYPE.APPLICATION_WITHDRAW'): // オンライン説明会申込キャンセル
                $title = '【オンライン説明会申込取下げ】';
                $content = '下記のオンライン説明会申込を取下げました。';
                break;
            default:
                break;
        }

        $title .= $theSchool->organization_name . " [" . $theHospital->organization_name . " / 病院説明会(募集期間：";
        $title .= $fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定";
        $title .= "-";
        $title .= $fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定";
        $title .= ")]";

        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        $appendInfoTypes = array_column(json_decode(json_encode($fair->fair_type), true), "fair_type");

        $content .= "\n";
        $content .= "\n【説明会情報】";
        $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
        $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
        $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
        $content .= "\n　募集期間　　　：" . ($fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定") . "～" . ($fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定");
        $content .= "\n";
        $content .= "\n【申込情報】";
        $content .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
        $content .= "\n　説明会形式　　：" . config('const.EVENT_TYPE_NAME')[$fairApplication->format];;
        $content .= "\n　参加予定人数　：" . $fairApplication->estimate_participant_number . "名";
        $content .= "\n";
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得(申込は管理組織と病院組織、キャンセルは管理組織と病院組織と学校組織)
        $query = Organization::query();
        $query->where('organization_id', $theHospital->organization_id) //病院組織
              ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        // オンライン説明会申込キャンセル
        if ($notification_type == config('const.NOTIFICATION_TYPE.APPLICATION_CANCEL')
         || $notification_type == config('const.NOTIFICATION_TYPE.APPLICATION_WITHDRAW')) 
        {
            $query->orWhere('organization_id', $theSchool->organization_id); //学校組織
        }
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }
        
        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }


    /**
     * オンライン説明会仮決定
     * @param String $notification_id
     * @param String $notification_type
     * @param String $event_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyOnlineEventTentative($notification_id, $notification_type, $event_id, $notification_at)
    {
        //オンラインイベントテーブルから、オンラインイベントを取得する
        $onlineEvent = OnlineEvent::withTrashed()->with(["event_member"])->find($event_id);

        //説明会テーブルから、説明会を取得する
        $fair = Fair::withTrashed()->with(["fair_type", "organization" => function($q){$q->withTrashed();}])->find($onlineEvent->fair_id);

        //組織テーブルから、開催病院組織を取得する
        $theHospital = $fair->organization;

        //説明会参加申込テーブルから、説明会参加申込を取得する
        $fairApplications = FairApplication::with(["organization" => function($q){$q->withTrashed();}])->where('fair_id', $onlineEvent->fair_id)->get();

        $eventMembers = $onlineEvent->event_member;

        //タイトルとメッセージの作成
        $title = 
        $title = '【オンライン説明会仮決定】[';
        $title .= $theHospital->organization_name . " / 病院説明会(実施期間：";
        $title .= $onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定";
        $title .= "-";
        $title .= $onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定";
        $title .= ")]";


        $content = '';
        $content_application = '';

        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        $appendInfoTypes = array_column(json_decode(json_encode($fair->fair_type), true), "fair_type");

        //【説明会情報】
        $content .= '下記のオンライン説明会が仮決定されました。';
        $content .= "\n";
        $content .= "\n【説明会情報】";
        $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
        $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
        $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
        $content .= "\n　実施期間　　　：" . ($onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定") . "～" . ($onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定");
        $content .= "\n";

        //【申込情報】
        //説明会申込みの内、当該オンラインイベントのメンバーである学校のみ
        $school_ids = [];
        foreach ($fairApplications as $fairApplication)
        {
            foreach ($eventMembers as $eventMember)
            {
                if ($fairApplication->school_id == $eventMember->organization_id)
                {
                    $theSchool = $fairApplication->organization;
                    $school_ids[] = $theSchool->organization_id;
                    if ($content_application == '')
                    {
                        $content_application .= "\n【申込情報】";
                    }
                    $content_application .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
                    $content_application .= "\n　説明会形式　　：" . config('const.EVENT_TYPE_NAME')[$fairApplication->format];;
                    $content_application .= "\n　参加予定人数　：" . $fairApplication->estimate_participant_number . "名";
                    $content_application .= "\n";
                }
            }
        }
        $content .= $content_application . "";
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        $query->whereIn('organization_id', $school_ids) //学校組織
              ->orWhere('organization_id', $theHospital->organization_id) //病院組織
              ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }
        
        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }


    /**
     * オンライン説明会正式決定(個別チャンネル登録)
     * @param String $notification_id
     * @param String $notification_type
     * @param String $event_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyOnlineEventOfficial($notification_id, $notification_type, $event_id, $notification_at)
    {
        //オンラインイベントテーブルから、オンラインイベントを取得する
        $onlineEvent = OnlineEvent::withTrashed()->with(["event_member" => function($q){$q->orderBy('member_role', 'desc');}, "event_member.organization" => function($q){$q->withTrashed();}, "estimate"])->find($event_id);
        
        //説明会テーブルから、説明会を取得する
        $fair = Fair::withTrashed()->with(["fair_type", "organization" => function($q){$q->withTrashed();}])->find($onlineEvent->fair_id);

        //組織テーブルから、開催病院組織を取得する
        $theHospital = null;

        //説明会参加申込テーブルから、説明会参加申込を取得する
        $fairApplications = FairApplication::with(["organization" => function($q){$q->withTrashed();}])->where('fair_id', $onlineEvent->fair_id)->get();

        $eventMembers = $onlineEvent->event_member;
        $estimate = $onlineEvent->estimate;

        $title = '';
        $content = '';
        $content_school = '';
        $content_hospital = '';
        $member_ids = [];
        $school_ids = [];

        //イベント種別が個別の場合(個別チャンネル登録/変更)
        if ($onlineEvent->event_type == config('const.EVENT_TYPE.INDIVIDUAL'))
        {
            $isNewEvent = $onlineEvent->created_at == $onlineEvent->updated_at;
            if ($isNewEvent) {
                $title .= '【個別チャンネル登録】[';
            } else {
                $title .= '【個別チャンネル変更】[';
            }
            foreach ($eventMembers as $eventMember) {
                if ($eventMember->member_role == config('const.MEMBER_ROLE.ORGANIZER')) {
                    $title .= $eventMember->organization->organization_name . " (実施期間：";
                }
            }
            $title .= $onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定";
            $title .= "-";
            $title .= $onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定";
            $title .= ")]";

            if ($isNewEvent) {
                $content .= "下記の個別チャンネルが登録されました。";
            } else {
                $content .= "下記の個別チャンネルが更新されました。";
            }
            
            $content .= "\n";
            $content .= "\n【チャンネル情報】";
            $content .= "\n　実施期間　　　：" . ($onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定") . "～" . ($onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定");
            foreach ($eventMembers as $eventMember) {
                if ($eventMember->member_role == config('const.MEMBER_ROLE.ORGANIZER')) {
                    $member_ids[] = $eventMember->organization->organization_id;
                    $content .= "\n　開催組織　　　：" . $eventMember->organization->organization_name . " (" . $eventMember->organization->prefecture . $eventMember->organization->city . ")";
                }
            }

            $content .= "\n";
            $content .= "\n【参加情報】";
            foreach ($eventMembers as $eventMember) {
                if ($eventMember->member_role != config('const.MEMBER_ROLE.ORGANIZER')) {
                    $org = $eventMember->organization;
                    $member_ids[] = $org->organization_id;
                    if ($org->organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL')) {
                        $content .= "\n　病院名　　　　：" . $org->organization_name . " (" . $org->prefecture . $org->city . ")";
                    } else if ($org->organization_type == config('const.ORGANIZATION_TYPE.SCHOOL')) {
                        $content .= "\n　学校名　　　　：" . $org->organization_name . " (" . $org->prefecture . $org->city . ")";
                    } else {
                        $content .= "\n　組織名　　　　：" . $org->organization_name . " (" . $org->prefecture . $org->city . ")";
                    }
                }
            }
            $content .= "\n";

            //【見積金額】
            $estimate_price = '';
            if ($estimate !== null)
            {
                $estimate_price .= "\n【見積金額】";
                $estimate_price .= "\n　" . number_format($estimate->estimate_price) . "円(税抜)";
                $estimate_price .= "\n";
            }

            $content_school .= $content . "";
            $content_hospital .= $content . $estimate_price . "";
        }
        else {
            $theHospital = $fair->organization;
            //タイトルとメッセージ(学校と病院で請求項目の有無)の作成
            $title .= '【オンライン説明会正式決定】[';
            $title .= $theHospital->organization_name . " / 病院説明会(実施期間：";
            $title .= $onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定";
            $title .= "-";
            $title .= $onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定";
            $title .= ")]";

            $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
            $appendInfoTypes = array_column(json_decode(json_encode($fair->fair_type), true), "fair_type");
    
            //【説明会情報】
            $content .= "下記のオンライン説明会が正式決定されました。";
            $content .= "\n";
            $content .= "\n【説明会情報】";
            $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
            $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
            $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
            $content .= "\n　実施期間　　　：" . ($onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定") . "～" . ($onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定");
            $content .= "\n";

            //【申込情報】
            //説明会申込みの内、当該オンラインイベントのメンバーである学校のみ
            $content_application = '';
            foreach ($fairApplications as $fairApplication)
            {
                foreach ($eventMembers as $eventMember)
                {
                    if ($fairApplication->school_id == $eventMember->organization_id)
                    {
                        $theSchool = $fairApplication->organization;
                        $school_ids[] = $theSchool->organization_id;
                        if ($content_application == '')
                        {
                            $content_application .= "\n【申込情報】";
                        }
                        $content_application .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
                        $content_application .= "\n　説明会形式　　：" . config('const.EVENT_TYPE_NAME')[$fairApplication->format];;
                        $content_application .= "\n　参加予定人数　：" . $fairApplication->estimate_participant_number . "名";
                        $content_application .= "\n";
                    }
                }
            }

            //【見積金額】
            $estimate_price = '';
            if ($estimate !== null)
            {
                $estimate_price .= "\n【見積金額】";
                $estimate_price .= "\n　" . number_format($estimate->estimate_price) . "円(税抜)";
                $estimate_price .= "\n";
            }

            $content_school .= $content . $content_application . "";
            $content_hospital .= $content . $content_application . $estimate_price . "";
        }
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content_school,
                'content_hospital'  => $content_hospital
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        //イベント種別が個別の場合(個別チャンネル登録)
        if ($onlineEvent->event_type == config('const.EVENT_TYPE.INDIVIDUAL'))
        {
            $query->whereIn('organization_id', $member_ids);
        } else {
            $query->whereIn('organization_id', $school_ids) //学校組織
            ->orWhere('organization_id', $theHospital->organization_id) //病院組織
            ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        }
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->organization_type == config('const.ORGANIZATION_TYPE.SCHOOL'))
            {
                $theContent = $content_school;
            } else {
                $theContent = $content_hospital;
            }

            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $theContent);
            }
        }
    }


    /**
     * オンライン説明会キャンセル
     * @param String $notification_id
     * @param String $notification_type
     * @param String $event_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyOnlineEventCancel($notification_id, $notification_type, $event_id, $notification_at)
    {
        //オンラインイベントテーブルから、オンラインイベントを取得する
        $onlineEvent = OnlineEvent::withTrashed()->with(["event_member", "estimate"])->find($event_id);
        
        //説明会テーブルから、説明会を取得する
        $fair = Fair::withTrashed()->with(["fair_type", "organization" => function($q){$q->withTrashed();}])->find($onlineEvent->fair_id);

        //組織テーブルから、開催病院組織を取得する
        $theHospital = $fair->organization;

        //説明会参加申込テーブルから、説明会参加申込を取得する
        $fairApplications = FairApplication::with(["organization" => function($q){$q->withTrashed();}])->where('fair_id', $onlineEvent->fair_id)->get();

        $eventMembers = $onlineEvent->event_member;
        $estimate = $onlineEvent->estimate;

        //実施期間があれば、実施期間。なければ、募集期間
        $at_flg = $onlineEvent->start_at && $onlineEvent->end_at;

        //タイトルとメッセージの作成
        $title = "";
        if ($at_flg)
        {
            $title .= '【オンライン説明会キャンセル】[';
            $title .= $theHospital->organization_name . " / 病院説明会(実施期間：";
            $title .= $onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定";
            $title .= "-";
            $title .= $onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定";
            $title .= ")]";
        }
        else 
        {
            $title .= '【オンライン説明会キャンセル】[';
            $title .= $theHospital->organization_name . " / 病院説明会(募集期間：";
            $title .= $fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定";
            $title .= "-";
            $title .= $fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定";
            $title .= ")]";
        }

        $content = '';
        $content_application = '';
        $content_school = '';
        $content_hospital = '';

        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        $appendInfoTypes = array_column(json_decode(json_encode($fair->fair_type), true), "fair_type");

        //【説明会情報】
        $content .= "下記のオンライン説明会がキャンセルされました。";
        $content .= "\n";
        $content .= "\n【説明会情報】";
        $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
        $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
        $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
        if ($at_flg)
        {
            $content .= "\n　実施期間　　　：" . ($onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定") . "～" . ($onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定");
        }
        else
        {
            $content .= "\n　募集期間　　　：" . ($fair->plan_start_at ? Carbon::createFromFormat($this->format, $fair->plan_start_at)->format('Y/m/d') : "未定") . "～" . ($fair->plan_end_at ? Carbon::createFromFormat($this->format, $fair->plan_end_at)->format('Y/m/d') : "未定");
        }
        $content .= "\n";

        //【申込情報】
        //説明会申込みの内、当該オンラインイベントのメンバーである学校のみ
        $school_ids = [];
        foreach ($fairApplications as $fairApplication)
        {
            foreach ($eventMembers as $eventMember)
            {
                if ($fairApplication->school_id == $eventMember->organization_id)
                {
                    $theSchool = $fairApplication->organization;
                    $school_ids[] = $theSchool->organization_id;
                    if ($content_application == '')
                    {
                        $content_application .= "\n【申込情報】";
                    }
                    $content_application .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
                    $content_application .= "\n　説明会形式　　：" . config('const.EVENT_TYPE_NAME')[$fairApplication->format];;
                    $content_application .= "\n　参加予定人数　：" . $fairApplication->estimate_participant_number . "名";
                    $content_application .= "\n";
                }
            }
        }

        //【見積金額】
        $estimate_price = '';
        if ($estimate !== null)
        {
            $estimate_price .= "\n【見積金額】";
            $estimate_price .= "\n　" . number_format($estimate->estimate_price) . "円(税抜)";
            $estimate_price .= "\n";
        }

        $content_school .= $content . $content_application . "";
        $content_hospital .= $content . $content_application . $estimate_price . "";
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content_school,
                'content_hospital'  => $content_hospital
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        $query->whereIn('organization_id', $school_ids) //学校組織
              ->orWhere('organization_id', $theHospital->organization_id) //病院組織
              ->orWhere('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->organization_type == config('const.ORGANIZATION_TYPE.SCHOOL'))
            {
                $theContent = $content_school;
            } else {
                $theContent = $content_hospital;
            }

            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $theContent);
            }
        }
    }


    /**
     * アンケート登録
     * @param String $notification_id
     * @param String $notification_type
     * @param String $questionary_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyQuestionary($notification_id, $notification_type, $questionary_id, $notification_at)
    {
        // 説明会アンケート取得
        $questionary = Questionary::with([
            'questionary_fair_types',
            'questionary_hospitals',
            'questionary_hospitals.organization' => function($q){$q->withTrashed();},
            'questionary_places',
            'questionary_hospital_types',
            'organization' => function($q){$q->withTrashed();}
        ])->find($questionary_id);

        $theSchool = $questionary->organization;

        
        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        $appendInfoTypes = array_column(json_decode(json_encode($questionary->questionary_fair_types), true), "fair_type");

        $hospitalOrganizations = array_column(json_decode(json_encode($questionary->questionary_hospitals), true), "organization");
        $hospitalNames = array_column(json_decode(json_encode($hospitalOrganizations), true), "organization_name");

        $hospitalPlaces = array_column(json_decode(json_encode($questionary->questionary_places), true), "place");

        $allHospitalTypes = config('const.HOSPITAL_TYPE_NAME');
        $hospitalTypes = array_column(json_decode(json_encode($questionary->questionary_hospital_types), true), "hospital_type");

        //タイトルとメッセージの作成
        $title = "【アンケート情報登録】" . $theSchool->organization_name;
        $content = "下記のアンケート情報が登録されました。";
        $content .= "\n";
        $content .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
        $content .= "\n　希望説明会種別：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
        $content .= "\n　希望実施期間　：" . ($questionary->desire_start_at ? Carbon::createFromFormat($this->format, $questionary->desire_start_at)->format('Y/m/d') : "未定") . "～" . ($questionary->desire_end_at ? Carbon::createFromFormat($this->format, $questionary->desire_end_at)->format('Y/m/d') : "未定");
        $content .= "\n　希望病院　　　：". implode("、", $hospitalNames);
        $content .= "\n　希望地域　　　：". implode("、", $hospitalPlaces);
        $content .= "\n　希望病院種別　：" . implode(" ", array_filter($allHospitalTypes, function($type) use ($hospitalTypes) {return in_array($type, $hospitalTypes);}, ARRAY_FILTER_USE_KEY));
        $content .= "\n";
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        $query->where('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')); //管理組織
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }

    
    /**
     * 請求情報通知
     * @param String $notification_id
     * @param String $notification_type
     * @param String $payment_id
     * @param Timestamp $notification_at
     * @return void
     */
    private function notifyPayment($notification_id, $notification_type, $payment_id, $notification_at)
    {
        // 請求取得
        $payment = Payment::with([
            'organization' => function($q){$q->withTrashed();},
            'organization.hospital',
            'payment_details',
            'payment_details.estimate',
            'payment_details.estimate.online_event',
            'payment_details.estimate.online_event.event_member',
            'payment_details.estimate.online_event.fair',
            'payment_details.estimate.online_event.fair.fair_type',
            'payment_details.estimate.online_event.fair.fair_applications',
            'payment_details.estimate.online_event.fair.fair_applications.organization' => function($q){$q->withTrashed();},
        ])->find($payment_id);

        //請求先病院
        $theHospital = $payment->organization;
        //請求明細
        $payment_details = $payment->payment_details;

        $allAppendInfoTypes = config('const.APPEND_INFO_TYPE_NAME');
        
        
        //タイトルとメッセージの作成
        $title = "【請求情報_" . Carbon::createFromFormat($this->format, $notification_at)->format('Ymd') . "】" . $theHospital->organization_name;
        $content = "下記のオンライン説明会に対する請求情報を通知します。";
        // 【請求金額】
        $content .= "\n【" . Carbon::createFromFormat($this->format, $payment->payment_month)->format('Y年m月') . "の請求金額】";
        $content .= "\n   " . number_format($payment->payment_price) . "円(税抜)";
        $content .= "\n";

        foreach ($payment_details as $payment_detail)
        {
            //見積
            $estimate = $payment_detail->estimate;
            //オンラインイベント
            $onlineEvent = $estimate->online_event;
            //イベントメンバー
            $eventMembers = $onlineEvent->event_member;
            //
            $fairApplications = $onlineEvent->fair->fair_applications;

            $appendInfoTypes = array_column(json_decode(json_encode($onlineEvent->fair->fair_type), true), "fair_type");

            // 【説明会情報】
            $content .= "\n【説明会情報】";
            $content .= "\n　病院名　　　　：" . $theHospital->organization_name . " (" . $theHospital->prefecture . $theHospital->city . ")";
            $content .= "\n　病院種別　　　：" . empty($theHospital->hospital->hospital_type) ? "" : config('const.HOSPITAL_TYPE_NAME')[$theHospital->hospital->hospital_type];
            $content .= "\n　説明会種別　　：" . implode(" ", array_filter($allAppendInfoTypes, function($type) use ($appendInfoTypes) {return in_array($type, $appendInfoTypes);}, ARRAY_FILTER_USE_KEY));
            $content .= "\n　実施期間　　　：" . ($onlineEvent->start_at ? Carbon::createFromFormat($this->format, $onlineEvent->start_at)->format('Y/m/d H:i') : "未定") . "～" . ($onlineEvent->end_at ? Carbon::createFromFormat($this->format, $onlineEvent->end_at)->format('Y/m/d H:i') : "未定");
            $content .= "\n";

            // 【申込情報】
            $content_application = '';
            foreach ($fairApplications as $fairApplication)
            {
                foreach ($eventMembers as $eventMember)
                {
                    if ($fairApplication->school_id == $eventMember->organization_id)
                    {
                        $theSchool = $fairApplication->organization;
                        if ($content_application == '')
                        {
                            $content_application .= "\n【申込情報】";
                        }
                        $content_application .= "\n　学校名　　　　：" . $theSchool->organization_name . " (" . $theSchool->prefecture . $theSchool->city . ")";
                        $content_application .= "\n　説明会形式　　：" . config('const.EVENT_TYPE_NAME')[$fairApplication->format];;
                        $content_application .= "\n　参加予定人数　：" . $fairApplication->estimate_participant_number . "名";
                        $content_application .= "\n";
                    }
                }
            }
            $content .= $content_application;

            //【見積金額】
            $estimate_price = '';
            if ($estimate !== null)
            {
                $estimate_price .= "\n【金額】";
                $estimate_price .= "\n　" . number_format($estimate->estimate_price) . "円(税抜)";
                $estimate_price .= "\n";
            }
            $content .= $estimate_price;
            $content .= "";
        }
        
        
        //通知登録
        Notification::create(
            [
                'notification_id'   => $notification_id,
                'notification_type' => $notification_type,
                'notification_at'   => $notification_at,
                'title'             => $title,
                'content_school'    => $content,
                'content_hospital'  => $content
            ]
        );

        //通知先組織取得
        $query = Organization::query();
        $query->where('organization_type', config('const.ORGANIZATION_TYPE.MANAGE')) //管理組織
              ->orWhere('organization_id', $theHospital->organization_id); //請求先病院
        $organizations = $query->get();

        //通知登録
        $notifications = array();
        //通知先組織ID
        $notificationOrganizationIds = array();

        $now = (new Carbon('', 'JST'));
        foreach ($organizations as $organization) {
            $notifications[] = array(
                "notification_id"   => $notification_id,
                "organization_id"   => $organization->organization_id,
                "confirm_status"    => false,
                "created_at"        => $now,
                "updated_at"        => $now
            );

            $notificationOrganizationIds[] = $organization->organization_id;
        }
        if (!empty($notifications))
        {
            $notificationsCollection = collect($notifications);
            $notificationsChunks = $notificationsCollection->chunk(50); //SQL Server supports a maximum of 2100 parametersのException対応
            foreach ($notificationsChunks as $notificationsChunk)
            {
                NotificationDestination::insert($notificationsChunk->toArray());
            }
        }

        //通知ブロック
        $notificationBlock = NotificationBlock::select(['organization_id'])->get();
        $blockOrganizationIds = array_column(json_decode(json_encode($notificationBlock), true), "organization_id");

        //通知先組織(通知ブロックに含む組織はメール通知除外する)
        $mailOrganizationIDs = [];
        foreach($notificationOrganizationIds as $notificationOrganizationId) {
            if (!in_array($notificationOrganizationId, $blockOrganizationIds))
            {
                $mailOrganizationIDs[] = $notificationOrganizationId;
            }
        }

        //メール取得
        $mails = Organization::with(["user"])->whereIn('organization_id', $mailOrganizationIDs)->get();

        foreach ($mails as $mail)
        {
            if ($mail->user)
            {
                $this->sendMail($mail->user->mail_address, $title, $content);
            }
        }
    }


    /**
     * メール送信
     * @param String $email
     * @param String $title
     * @param String $content
     * @return void
     */
    private function sendMail($email, $title, $content)
    {
        //メール送信
        Mail::send([], [], function ($message) use ($email, $title, $content) {
			//送信先
            $message->to($email);
            //件名
            $message->subject($title);
            //内容
            $message->setBody($content);
        });
    }
}
