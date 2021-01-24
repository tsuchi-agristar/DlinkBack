<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'v1/user', 'as' => 'user.' ], function() {
    Route::post('/', ['as' => 'createUser', 'uses' => 'UserController@createUser']);
    Route::put('/{user_id}', ['as' => 'updateUserById', 'uses' => 'UserController@updateUserById']);
    Route::delete('/{user_id}', ['as' => 'deleteUserById', 'uses' => 'UserController@deleteUserById']);
    Route::post('/login', ['as' => 'login', 'uses' => 'UserController@login']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/schoolActivity', 'as' => 'schoolActivity.' ], function() {
    Route::get('/', ['as' => 'getSchoolActivity', 'uses' => 'SchoolActivityController@getSchoolActivity']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/school', 'as' => 'school.' ], function() {
    Route::get('/', ['as' => 'getSchool', 'uses' => 'SchoolController@getSchool']);
    Route::post('/', ['as' => 'addSchool', 'uses' => 'SchoolController@addSchool']);
    Route::get('/{school_id}', ['as' => 'getSchoolById', 'uses' => 'SchoolController@getSchoolById']);
    Route::put('/{school_id}', ['as' => 'updateSchoolById', 'uses' => 'SchoolController@updateSchoolById']);
    Route::delete('/{school_id}', ['as' => 'deleteSchoolById', 'uses' => 'SchoolController@deleteSchoolById']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/questionary', 'as' => 'questionary.' ], function() {
    Route::post('/', ['as' => 'createQuestionary', 'uses' => 'QuestionaryController@createQuestionary']);
    Route::get('/', ['as' => 'getQuestionary', 'uses' => 'QuestionaryController@getQuestionary']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/payment', 'as' => 'payment.' ], function() {
    Route::get('/', ['as' => 'getPayment', 'uses' => 'PaymentController@getPayment']);
    Route::get('/{payment_id}', ['as' => 'getpaymentById', 'uses' => 'PaymentController@getpaymentById']);
    Route::put('/{payment_id}', ['as' => 'updatePaymentById', 'uses' => 'PaymentController@updatePaymentById']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/organization', 'as' => 'organization.' ], function() {
    Route::get('/', ['as' => 'getOrganization', 'uses' => 'OrganizationController@getOrganization']);
    Route::post('/', ['as' => 'addOrganization', 'uses' => 'OrganizationController@addOrganization']);
    Route::get('/{organization_id}', ['as' => 'getOrganizationById', 'uses' => 'OrganizationController@getOrganizationById']);
    Route::put('/{organization_id}', ['as' => 'updateOrganizationById', 'uses' => 'OrganizationController@updateOrganizationById']);
    Route::delete('/{organization_id}', ['as' => 'deleteOrganizationById', 'uses' => 'OrganizationController@deleteOrganizationById']);
    Route::get('/{organization_id}/user', ['as' => 'getOrganizationUserById', 'uses' => 'OrganizationController@getOrganizationUserById']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/onlineEvent', 'as' => 'onlineEvent.' ], function() {
    Route::get('/', ['as' => 'getOnlineEvent', 'uses' => 'OnlineEventController@getOnlineEvent']);
    Route::post('/', ['as' => 'createOnlineEvent', 'uses' => 'OnlineEventController@createOnlineEvent']);
    Route::get('/{event_id}', ['as' => 'getOnlineEventById', 'uses' => 'OnlineEventController@getOnlineEventById']);
    Route::put('/{event_id}', ['as' => 'updateOnlineEventById', 'uses' => 'OnlineEventController@updateOnlineEventById']);
    Route::delete('/{event_id}', ['as' => 'deleteOnlineEventById', 'uses' => 'OnlineEventController@deleteOnlineEventById']);
    Route::get('/{event_id}/estimate', ['as' => 'getEstimateOfEvent', 'uses' => 'OnlineEventController@getEstimateOfEvent']);
});

// fairへ統合のため不要
//Route::group(['namespace' => 'Api', 'prefix' => 'v1/hospitalAppend', 'as' => 'hospitalAppend.' ], function() {
//    Route::post('/', ['as' => 'createHospitalAppend', 'uses' => 'HospitalAppendController@createHospitalAppend']);
//    Route::put('/', ['as' => 'updateHospitalAppend', 'uses' => 'HospitalAppendController@updateHospitalAppend']);
//    Route::delete('/{append_information_id}', ['as' => 'deleteHospitalAppendById', 'uses' => 'HospitalAppendController@deleteHospitalAppendById']);
//});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/hospitalActivity', 'as' => 'hospitalActivity.' ], function() {
    Route::get('/', ['as' => 'getHospitalActivity', 'uses' => 'HospitalActivityController@getHospitalActivity']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/hospital', 'as' => 'hospital.' ], function() {
    Route::get('/', ['as' => 'getHospital', 'uses' => 'HospitalController@getHospital']);
    Route::post('/', ['as' => 'addHospital', 'uses' => 'HospitalController@addHospital']);
    Route::get('/{hospital_id}', ['as' => 'getHospitalById', 'uses' => 'HospitalController@getHospitalById']);
    Route::put('/{hospital_id}', ['as' => 'updateHospitalById', 'uses' => 'HospitalController@updateHospitalById']);
    Route::delete('/{hospital_id}', ['as' => 'deleteHospitalById', 'uses' => 'HospitalController@deleteHospitalById']);
    Route::get('/{hospital_id}/append', ['as' => 'getHospitalAppend', 'uses' => 'HospitalController@getHospitalAppend']);
    Route::get('/{hospital_id}/fair', ['as' => 'getHospitalFair', 'uses' => 'HospitalController@getHospitalFair']);

});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/fairDetail', 'as' => 'fairDetail.' ], function() {
    Route::post('/', ['as' => 'createFairDetail', 'uses' => 'FairDetailController@createFairDetail']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/fairApplication', 'as' => 'fairApplication.' ], function() {
    Route::get('/', ['as' => 'getFairApplication', 'uses' => 'FairApplicationController@getFairApplication']);
    Route::post('/', ['as' => 'createFairApplication', 'uses' => 'FairApplicationController@createFairApplication']);
    Route::get('/{application_id}', ['as' => 'getFairApplicationById', 'uses' => 'FairApplicationController@getFairApplicationById']);
    Route::put('/{application_id}', ['as' => 'updateFairApplicationById', 'uses' => 'FairApplicationController@updateFairApplicationById']);
    Route::get('/{application_id}/fair', ['as' => 'getFairOfApplication', 'uses' => 'FairApplicationController@getFairOfApplication']);
    Route::delete('/{application_id}', ['as' => 'deleteFairApplication', 'uses' => 'FairApplicationController@deleteFairApplication']);
    Route::get('/{fair_id}/application', ['as' => 'getApplicationOfFairApplication', 'uses' => 'FairApplicationController@getApplicationOfFairApplication']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/fair', 'as' => 'fair.' ], function() {
    Route::get('/', ['as' => 'getFair', 'uses' => 'FairController@getFair']);
    Route::post('/', ['as' => 'addFair', 'uses' => 'FairController@addFair']);
    Route::get('/{fair_id}', ['as' => 'getFairById', 'uses' => 'FairController@getFairById']);
    Route::put('/{fair_id}', ['as' => 'updateFairById', 'uses' => 'FairController@updateFairById']);
    Route::delete('/{fair_id}', ['as' => 'deleteFairById', 'uses' => 'FairController@deleteFairById']);
    Route::get('/{fair_id}/application', ['as' => 'getApplicationOfFair', 'uses' => 'FairController@getApplicationOfFair']);
    Route::get('/{fair_id}/onlineevent', ['as' => 'getOnlineEventOfFair', 'uses' => 'FairController@getOnlineEventOfFair']);
    Route::get('/{fair_id}/detail', ['as' => 'getDetailOfFair', 'uses' => 'FairController@getDetailOfFair']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/eventMember', 'as' => 'eventMember.' ], function() {
    Route::get('/{organization_id}/channel', ['as' => 'getChannelbyMemberId', 'uses' => 'EventMemberController@getChannelbyMemberId']);
    Route::get('/{event_id}', ['as' => 'getEventMemberById', 'uses' => 'EventMemberController@getEventMemberById']);
    Route::put('/{event_id}', ['as' => 'updateEventMember', 'uses' => 'EventMemberController@updateEventMember']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/estimate', 'as' => 'estimate.' ], function() {
    Route::post('/', ['as' => 'createEstimate', 'uses' => 'EstimateController@createEstimate']);
    Route::put('/{estimate_id}', ['as' => 'updateEstimateById', 'uses' => 'EstimateController@updateEstimateById']);
    Route::delete('/{estimate_id}', ['as' => 'deleteEstimateById', 'uses' => 'EstimateController@deleteEstimateById']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/service', 'as' => 'service.' ], function() {
    Route::get('/', ['as' => 'getService', 'uses' => 'ServiceController@getService']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/notificationblock', 'as' => 'notificationblock.' ], function() {
    Route::post('/', ['as' => 'createNotificationblock', 'uses' => 'NotificationBlockController@createNotificationblock']);
    Route::delete('/{organization_id}', ['as' => 'deleteNotificationblockById', 'uses' => 'NotificationBlockController@deleteNotificationblockById']);
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1/notification', 'as' => 'notification.' ], function() {
    Route::post('/', ['as' => 'createNotification', 'uses' => 'NotificationController@createNotification']);
    Route::get('/{organization_id}', ['as' => 'getNotificationsByOrganizationId', 'uses' => 'NotificationController@getNotificationsByOrganizationId']);
    Route::get('/{organization_id}/{noticication_id}', ['as' => 'getNotification', 'uses' => 'NotificationController@getNotification']);
    Route::put('/', ['as' => 'updateNotification', 'uses' => 'NotificationController@updateNotification']);
    Route::put('/{organization_id}', ['as' => 'updateNotificationOfOrganization', 'uses' => 'NotificationController@updateNotificationOfOrganization']);
});
