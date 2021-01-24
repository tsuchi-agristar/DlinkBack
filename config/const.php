<?php

return [

    // 検索デフォルトページ番号
    'DEFAULT_PAGE_NUM' => 0,
    // 検索デフォルトページサイズ
    'DEFAULT_PAGE_SIZE' => 0,

    // 組織タイプ
    'ORGANIZATION_TYPE' => [
        'MANAGE' => 1,
        'HOSPITAL' => 2,
        'SCHOOL' => 3,
    ],

    // 学校種別
    'SCHOOL_TYPE' => [
        'UNIVERSITY' => 1, // 大学
        'APECIALTY' => 2, // 専門
        'HIGH' => 3, //高校
    ],
    // 病院種別
    'HOSPITAL_TYPE' => [
        'UNIVERSITY' => 1,  // 大学病院
        'PUBLIC' => 2,      // 公的病院
        'ACUTE' => 3,       // 急性期病院
        'MEDICAL' => 4,     // 療養型病院
        'CARE' => 5,        // ケアミックス病院
        'REHA' => 6         // リハビリテーション病院
    ],
    // 病院種別名
    'HOSPITAL_TYPE_NAME' => [
        1 => '大学病院',
        2 => '公的病院',
        3 => '急性期病院',
        4 => '療養型病院',
        5 => 'ケアミックス病院',
        6 => 'ハビリテーション病院',
    ],
    // メンバーロール
    'MEMBER_ROLE' => [
        'OTHER' => 0,
        'ORGANIZER' => 1   // 主催者
    ],
    // イベント種別
    'EVENT_TYPE' => [
        'INDIVIDUAL' => 1,  // 個別
        'CLASS' => 2,       // 授業
        'SMALL' => 3,        // 少人数
        'SIMPLE' => 4        // 簡易
    ],
    // イベント種別名
    'EVENT_TYPE_NAME' => [
        1 => '個別',
        2 => '授業',
        3 => '少人数',
        4 => '簡易'
    ],

    // イベント状態
    'EVENT_STATUS' => [
        'UNDECIDED' => 0,   // 未決定
        'TENTATIVE' => 1,   // 仮決定
        'OFFICIAL' => 2,    // 正式決定
        'CANCEL' => 3,      // キャンセル
        'DONE' => 4,        // 完了
        'DONE_NO_PAY' => 5, // 完了(未請求)
    ],
    // チャンネル状態
    'CHANNEL_STATUS' => [
        'OPEN' => 1,        // OPEN
        'CLOSE' => 2        // CLOSE
    ],
    // 説明会状態
    'FAIR_STATUS' => [
        'RECRUITING' => 1,   // 募集中
        'END' => 2,          // 募集終了
        'CANCEL' => 3        // キャンセル
    ],
    // 説明会種別
    'FAIR_TYPE' => [
        'HOSPITAL_SESSION' => 1,   // 病院紹介
        'MANGER_SESSION' => 2,  // 看護部長の話
        'SENIOR_SESSION' => 3,  // 先輩看護師の話
        'OTHER_SESSION' => 4   // その他
    ],

    // 申込状態
    'APPLICATION_STATUS' => [
        'APPLYING' => 1,     // 申込中
        'DONE' => 2,         // 申込済
        'CANCEL' => 3,       // キャンセル
        'WITHDRAW' => 4      // 取下げ
    ],

    // 見積状態
    'ESTIMATE_STATUS' => [
        'UNDECIDED' => 0,   // 未決定
        'TENTATIVE' => 1,   // 仮決定
        'OFFICIAL' => 2,    // 最終決定済
    ],

    // 付属情報種別
    'APPEND_INFO_TYPE' => [
        'SCHOLARSHIP' => 1 , // 奨学金情報
        'INTERSHIP' => 2, // インターシップ情報
        'PRACTICE' => 3, // 実習情報
        'FAIR' => 4 // 病院説明会
    ],
    // 付属情報種別名
    'APPEND_INFO_TYPE_NAME' => [
        1 => '奨学金情報',
        2 => 'インターシップ情報',
        3 => '実習情報',
        4 => '病院説明会',
    ],
    // 募集職種一覧
    'JOB_TYPE' => [
        "REGULAR" => 1, // 看護師
        "SEMI" => 2, // 準看護師
    ],

    // 請求状態
    'PAYMENT_STATUS' => [
        'TENTATIVE' => 1,   // 仮決定
        'OFFICIAL' => 2,    // 最終決定済
    ],

    // 通知種別
    'NOTIFICATION_TYPE' => [
        "ORGANIZATION_REGISTER" => 1, // 新規登録
        "FAIR_REGISTER" => 2, // オンライン説明会登録
        "FAIR_MODIFY" => 3, // オンライン説明会変更
        "FAIR_DELETE" => 4, // オンライン説明会削除
        "APPLICATION_REGISTER" => 5, // オンライン説明会申込
        "APPLICATION_CANCEL" => 6, // オンライン説明会申込キャンセル
        "APPLICATION_WITHDRAW" => 7, // オンライン説明会申込取下げ
        "ONLINE_EVENT_TENTATIVE" => 8, // オンライン説明会仮決定
        "ONLINE_EVENT_OFFICIAL" => 9, // オンライン説明会正式決定
        "ONLINE_EVENT_CANCEL" => 10, // オンライン説明会キャンセル
        "QUESTIONARY_REGISTER" => 11, // アンケート登録
        "PAYMENT_REGISTER" => 12, // 請求情報通知
    ],

    // 通知未読既読
    'NOTIFICATION_STATUS' => [
        "READED" => true, // 既読
        "UNREAD" => false, // 未読
    ],
];
