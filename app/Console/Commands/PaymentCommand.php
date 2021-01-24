<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OnlineEvent;
use App\Models\Payment;
use App\Models\PaymentDetail;

class PaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment Batch';

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
        \Log::info('[PaymentCommand Start] ' . __METHOD__ . '::' . (__LINE__) );

        //請求月の確定
        $payment_date = Carbon::parse('-1 month')->endOfMonth();

        //請求明細テーブルに登録済の見積ID(下記で当該見積IDに関連する見積は除外するため)
        $exceptEstimateIds = PaymentDetail::select('estimate_id')->get();

        //オンラインイベントテーブルからオンラインイベント(複数)を取得する
        // 　※イベント状態: 完了
        // 　※決定終了日時: 1で求めた請求前月の末日以内（例. 請求月が2020年02月01日の場合、決定終了日時<=2020年01月31日）
        // 　※見積テーブルと関連付くイベントのみ
        // 　　・見積のイベント: オンラインイベントのイベントID
        // 　　・見積の見積状態: 最終決定済
        // 　　・請求明細テーブルと関連付いていない見積のみ ※見積の見積IDが請求明細の見積IDに存在しない
        $onlineEvents = OnlineEvent::with(["estimate", "event_member", "event_member.organization"])
            ->where('onlineevents.event_status', '=', config('const.EVENT_STATUS.DONE'))
            ->where('onlineevents.end_at', '<=', Carbon::parse('-1 month')->endOfMonth())
            ->rightJoin('estimates', 'onlineevents.event_id', '=', 'estimates.event_id')
            ->where('estimates.estimate_status', '=', config('const.ESTIMATE_STATUS.OFFICIAL'))
            ->whereNotIn('estimates.estimate_id', array_column(json_decode(json_encode($exceptEstimateIds), true), "estimate_id"))
            ->get();

        //データ整理(オンラインイベント単位の病院IDと見積金額)
        $datas = [];
        foreach ($onlineEvents as $onlineEvent)
        {
            $eventMembers = $onlineEvent->event_member;
            $estimate = $onlineEvent->estimate;
            if ($eventMembers)
            {
                foreach ($eventMembers as $eventMember)
                {
                    if ($eventMember->member_role == config('const.MEMBER_ROLE.ORGANIZER')
                     && $eventMember->organization->organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL'))
                    {
                        $datas[] = array(
                            "organization_id" => $eventMember->organization_id,
                            "estimate_price" => $estimate->estimate_price,
                            "estimate_id" => $estimate->estimate_id
                        );
                        break;
                    }
                }
            }
        }

        //$datasから重複を除いた開催病院ID
        $organizationIDs = array_unique(array_column(json_decode(json_encode($datas), true), "organization_id"));

        //開催病院ID単位に見積の見積金額を合算、また見積IDを纏める
        $paymentDatas = [];
        foreach ($organizationIDs as $organizationID)
        {
            $payment_id = (string)Str::uuid();
            $payment_price = 0;
            $estimate_ids = [];
            foreach ($datas as $data)
            {
                if ($data["organization_id"] == $organizationID)
                {
                    $payment_price += $data["estimate_price"];
                    $estimate_ids[] = $data["estimate_id"];
                }
            }

            $paymentDatas[] = array(
                "payment_id" => $payment_id,
                "payment_hospital_id" => $organizationID,
                "payment_month" => $payment_date->format('Y-m-d'),
                "payment_status" => config('const.PAYMENT_STATUS.TENTATIVE'),
                "payment_price" => $payment_price,
                "estimate_id" => $estimate_ids
            );
        }
        
        DB::beginTransaction();
        try {
            foreach ($paymentDatas as $paymentData)
            {
                //請求テーブル
                Payment::create($paymentData);

                $estimate_ids = $paymentData["estimate_id"];
                foreach ($estimate_ids as $estimate_id)
                {
                    //請求明細テーブル
                    PaymentDetail::create(
                        [
                            "payment_id" => $paymentData["payment_id"],
                            "estimate_id" => $estimate_id
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::critical($e);
        }
        DB::commit();

        \Log::info('[PaymentCommand End] ' . __METHOD__ . '::' . (__LINE__) );
        return true;
    }
}
