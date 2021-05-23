<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\Authenticate;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->intended('dashboard');
});

Auth::routes();

Route::get('/login', 'AuthController')->name('login');
Route::post('/login', 'AuthController@login');
Route::get('/logout', 'AuthController@logout')->name('logout');

Route::get('/forgot', function() { return view('pages.forgot'); })->name('forgot');
Route::post('/forgot', 'AuthController@forgot');

Route::get('/verify/google/auth', 'AuthController@forgot');

Route::get('/check', 'DashboardController@checks');

Route::group(['middleware' => ['login']], function () {

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/data/v1/dashboard/activeloan', 'DashboardController@activeLoan');
    Route::get('/data/v1/dashboard/settleloan', 'DashboardController@settleLoan');
    Route::get('/data/v1/dashboard/yearsettleloan', 'DashboardController@yearSettleLoan');

    Route::middleware(['deal.permission'])->group(function () {
        Route::get('/pipeline', 'PipelineController@index')->name('pipeline');

        Route::get('/deal/index/{id?}', 'DealController@index');
        Route::post('/deal/create', 'DealController@create');
        Route::post('/deal/update/{id}', 'DealController@update');
        Route::post('/deal/clone/{id}', 'DealController@clone');

        Route::get('/dealContact/{action}/{id?}', 'ContactController@index');
        Route::post('/dealContact/createcontact', 'ContactController@create');
        Route::post('/dealContact/getlist', 'ContactController@getContacts');

        Route::get('/loansplit/{action}/{id?}', 'LoanSplitController@loanSplit');
        Route::post('/loansplit/createloanentry', 'LoanSplitController@saveLoanSplit');
        Route::post('/loansplit/getlist', 'LoanSplitController@getLoanSplits');

        Route::post('/fileManagement/getlist', 'FileManagementController@getList');
        Route::post('/fileManagement/upload', 'FileManagementController@uploadFile');
        Route::get('/fileManagement/delete/{id?}', 'FileManagementController@deleteFile');
        Route::get('/fileManagement/download/{id}', 'FileManagementController@downLoadFile')->middleware('file.permission');

        /*--- API DATA ---*/
        Route::get('/data/v1/deal', 'DealController@show');

        Route::get('/data/v1/deal/notification/{id}', 'NotificationController@notification');
        Route::post('/data/v1/deal/notification/{id}', 'NotificationController@createNotification');
        Route::delete('/data/v1/deal/notification/{id}', 'NotificationController@deleteNotification');

        Route::get('/data/v1/reminder', 'ReminderController@reminder');
        Route::post('/data/v1/reminder', 'ReminderController@createReminder');
        Route::post('/data/v1/reminder/complete', 'ReminderController@completeReminder');
        Route::post('/data/v1/reminder/repeat', 'ReminderController@repeatReminder');
        Route::delete('/data/v1/reminder/delete/{id}', 'ReminderController@deleteReminder');



        Route::get('/data/v1/journalEntry', 'JournalController@journalEntry');
        Route::post('/data/v1/journalEntry', 'JournalController@createJournalEntry');

        Route::get('/data/v1/contact/autocomplete', 'ContactController@searchContactList');
        Route::get('/data/v1/contact/dealcontact', 'ContactController@searchDealContacttList');
    });

    Route::get('/reminder', 'ReminderController@index')->name('reminder');
    Route::get('/reminder/form/{id}', 'ReminderController@form');
    Route::get('/data/v1/reminder/datatable', 'ReminderController@reminderDatatable');
    Route::get('/data/v1/reminder/list', 'ReminderController@reminderDatalist');

    Route::get('/journal', 'JournalController@index')->name('journal');
    Route::get('/data/v1/journal/datatable', 'JournalController@journalDatatable');
    Route::get('/journal/csv', 'JournalController@csv');

    Route::get('/panel', 'PanelController@index')->name('panel');

    Route::get('/lead', 'LeadController@index')->name('lead');
    Route::get('/data/v1/lead/datatable', 'LeadController@leadDatatable');
    Route::get('/lead/csv', 'LeadController@csv');

    Route::get('/whiteboard', 'WhiteboardController@index')->name('whiteboard');
    Route::get('/whiteboard/combined', 'WhiteboardController@combined');
    Route::get('/whiteboard/basic', 'WhiteboardController@basic');
    Route::get('/whiteboard/business', 'WhiteboardController@business');
    Route::get('/whiteboard/marketing', 'WhiteboardController@marketing');
    Route::get('/whiteboard/csv', 'WhiteboardController@csv');

    Route::get('/team', 'TeamController@index')->name('team');
    Route::get('/team/brokers', 'TeamController@broker');
    Route::get('/team/pipeline', 'TeamController@pipeline');
    Route::get('/team/combined', 'TeamController@combined');
    Route::get('/team/basic', 'TeamController@basic');
    Route::get('/team/csv', 'TeamController@csv');

    Route::get('/calendar', 'CalendarController@index')->name('calendar');
    Route::get('/data/v1/calendar', 'CalendarController@calendar');
    Route::get('/data/v1/calendar/event', 'CalendarController@event');

    Route::get('/gcontact', 'ContactsController@gcontact');
    Route::get('/data/v1/gcontact/datatable', 'ContactsController@gdatatable');
    Route::get('/gcontact/create', 'ContactsController@gcreate');
    Route::post('/gcontact/create', 'ContactsController@gcreate');
    Route::get('/gcontact/edit/{id}', 'ContactsController@gedit');
    Route::post('/gcontact/edit/{id}', 'ContactsController@gedit');
    Route::get('/gcontact/delete/{id}', 'ContactsController@gdelete');

});

Route::get('/templates/{path}/{temp}', 'TempController@show');

Route::get('/p/create', 'ElasticController@create');
Route::get('/p/reset', 'ElasticController@reset');
Route::get('/p/flush', 'ElasticController@flush');
Route::get('/p/import', 'ElasticController@import');
Route::get('/p/full', 'ElasticController@full');
Route::get('/p/cron', 'ElasticController@cron');
Route::get('/p/list', 'ElasticController@list');


Route::get('/data/v1/count/{model}', 'ElasticController@state');


Route::get('/search', 'SearchController@index')->name('search');

Route::get('/data/v1/count/{model}', 'ElasticController@state');
/*
 * pages: configuration,
 * creted by Aleksey on 7/4/2020
 */
Route::group(['middleware' => ['login']], function () {
    Route::get('/configuration', 'ConfigurationController@index')->name('configuration');
    Route::post('/configuration/profile/{action?}/{id?}', 'ConfigurationController@profile')->name('configuration.profile');
    Route::get('/configuration/profile/{action?}/{id?}', 'ConfigurationController@profile')->name('configuration.profile.get');
    Route::get('/data/v1/user/files', 'ConfigurationController@userfiles');

    Route::get('/configuration/user', 'ConfigurationController@user');
    Route::get('/configuration/user/create', 'ConfigurationController@createUser');
    Route::post('/configuration/user/create', 'ConfigurationController@createUser');
    Route::get('/data/v1/user', 'ConfigurationController@userlist');
    Route::get('/data/v1/usertree', 'ConfigurationController@userTreelist');
    Route::get('/user/changepassword', 'ConfigurationController@changepassword');
    Route::get('/configuration/user/edit/{id}', 'ConfigurationController@editUser');
    Route::post('/configuration/user/edit/{id}', 'ConfigurationController@editUser');
    Route::post('/user/update_password', 'ConfigurationController@updatePassword');
    Route::post('/user/passwordChangeSuccess', 'ConfigurationController@passwordChangeSuccess');
    Route::post('/configuration/user/lockout', 'ConfigurationController@UserLockout');
    Route::post('/configuration/user/unlock', 'ConfigurationController@UserUnlock');
    Route::post('/configuration/user/resetpassword', 'ConfigurationController@Resetpassword');
    Route::post('/configuration/user/copydeal', 'ConfigurationController@CopyDeal');

    Route::get('/configuration/aggregator', 'ConfigurationController@aggregator');
    Route::get('/data/v1/aggregator', 'ConfigurationController@getAggregator');
    Route::get('/configuration/aggregator/create', 'ConfigurationController@createAggregator');
    Route::get('/configuration/aggregator/edit/{id}', 'ConfigurationController@editAggregator');
    Route::post('/configuration/aggregator/create', 'ConfigurationController@createAggregator');
    Route::post('/configuration/aggregator/edit/{id}', 'ConfigurationController@editAggregator');

    Route::get('/configuration/process', 'ConfigurationController@process');
    Route::get('/data/v1/process', 'ConfigurationController@getProcess');
    Route::get('/configuration/process/create', 'ConfigurationController@createProcess');
    Route::post('/configuration/process/create', 'ConfigurationController@createProcess');
    Route::get('/configuration/process/edit/{id}', 'ConfigurationController@editProcess');
    Route::post('/configuration/process/edit/{id}', 'ConfigurationController@editProcess');

    Route::get('/configuration/organisation', 'ConfigurationController@organisation');
    Route::get('/data/v1/organisation', 'ConfigurationController@getOrganisation');
    Route::get('/configuration/organisation/create', 'ConfigurationController@createOrganisation');
    Route::post('/configuration/organisation/create', 'ConfigurationController@createOrganisation');
    Route::get('/configuration/organisation/edit/{id}', 'ConfigurationController@editOrganisation');
    Route::post('/configuration/organisation/edit/{id}', 'ConfigurationController@editOrganisation');

    Route::get('/configuration/systemTasks', 'ConfigurationController@systemTasks');
    Route::get('/data/v1/task', 'ConfigurationController@getTask');
    Route::post('/data/v1/task', 'ConfigurationController@saveTask');

    Route::get('/report', 'ReportController@index');
    Route::get('/data/v1/reports/NestedReferrer', 'ReportController@NestedReferrerApi');

    Route::get('/scribble', 'ScribbleController@index');
    Route::get('/data/v1/scribble', 'ScribbleController@scribble');
    Route::put('/data/v1/scribble', 'ScribbleController@editScribble');
    Route::put('data/v1/scribble/delete', 'ScribbleController@deleteScribble');
    Route::post('data/v1/scribble/create', 'ScribbleController@createScribble');
    Route::put('data/v1/scribble/update', 'ScribbleController@updateScribble');
    Route::post('data/v1/scribble/sort', 'ScribbleController@sortScribble');
    Route::put('data/v1/scribble/category', 'ScribbleController@editCategory');
    Route::post('data/v1/scribble/category', 'ScribbleController@saveCategory');

    Route::get('/contact', 'ContactsController@index');
    Route::get('/data/v1/contact/datatable', 'ContactsController@datatable');
    Route::get('/contact/edit/{id}', 'ContactsController@edit');
    Route::post('/contact/edit/{id}', 'ContactsController@edit');
    Route::get('/contact/create', 'ContactsController@create');
    Route::post('/contact/create', 'ContactsController@create');
    //Route::get('/data/v1/contact/autocomplete', 'ContactsController@autocomplete');
    Route::get('/data/v1/contact', 'ContactsController@delvalidate');
    Route::get('/contact/delete/{id}', 'ContactsController@delete');
});

Route::middleware(['auth.api'])->group(function () {
    Route::group(['prefix' => '/api/v1'], function () {
        Route::get('/deals', 'API\DealController@getDeals');
    });
});
