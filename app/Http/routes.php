<?php
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}
use App\Http\Controllers\MeetingAgendaController;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

/*
 *  Create HTML macro for active class
//  */

Route::get('dashboard', 'MeetingAgendasController@index');
Route::get('agenda/show/{id}', 'MeetingAgendasController@show')->name('agenda.show');
Route::get('meetingagendas/create', 'MeetingAgendasController@create')->name('createmeetingagenda.create');
Route::post('meetingagendas/store', 'MeetingAgendasController@store')->name('storemeetingagendas.store');
Route::get('/meeting_agenda/{id}/edit', 'MeetingAgendasController@edit')->name('editmeetingagenda.edit');
Route::put('/meeting_agenda/update/{id}', 'MeetingAgendasController@update')->name('updatemeetingagenda.update');
Route::get('/meeting_agenda/{id}', 'MeetingAgendasController@destroy')->name('deletemeetingagenda.delete');
Route::get('agendas/show_2/{id}', 'MeetingAgendasController@show_2')->name('agendas.show_2');


Route::get('dashboard/meeting_types', 'MeetingTypeController@index');
Route::get('meeting_types/create', 'MeetingTypeController@create')->name('createmeetingtype.create');
Route::post('meeting_types/store', 'MeetingTypeController@store')->name('storemeetingtype.store');
Route::get('/meetingtypes/edit/{id}', 'MeetingTypeController@edit')->name('editmeetingtype.edit');
Route::put('/meetingtype/update/{id}', 'MeetingTypeController@update')->name('updatemeetingtype.update');
Route::get('/meeting-delete/{id}', 'MeetingTypeController@destroy')->name('meeting-delete.destroy');
Route::get('/meetingtypes/show/{id}', 'MeetingTypeController@show')->name('meetingType-show.show');
// Route::get('/meeting','MeetingAgendasController@index');

// Route::get('dashboard/wing', 'WingController@index');
// Route::get('wingcreate', 'WingController@create')->name('wingcreate.create');
// Route::post('wingstore', 'WingController@store')->name('wingstore.store');
// Route::get('/editwing/{id}', 'WingController@edit')->name('editwing.edit');
// Route::get('/wing/show/{id}', 'WingController@show')->name('wing.show.show');
// Route::put('/updatewing/{id}', 'WingController@update')->name('editwing.update');
// Route::get('/deletewing/{id}', 'WingController@destroy')->name('wing.destroy');

Route::get('dashboard/docstandard','DocStandardController@index');
Route::get('docstandard/create', 'DocStandardController@create')->name('docstandardcreate.create');
Route::post('docstandard/store', 'DocStandardController@store')->name('docstandardstore.store');
Route::get('/docstandard/edit/{id}', 'DocStandardController@edit')->name('docstandard.edit');
Route::put('/docstandard/update/{id}', 'DocStandardController@update')->name('docstandard.update');
Route::get('/deletedocument/{id}', 'DocStandardController@destroy')->name('documentstandard.destroy');
Route::get('/docstandard/show{id}', 'DocStandardController@show')->name('documentstandard.show');

Route::get('dashboard/meeting','MeetingController@index');
Route::get('meeting/create', 'MeetingController@create')->name('meetingcreate.create');
Route::post('meeting/store', 'MeetingController@store')->name('meetingstore.store');
Route::get('/meeting/edit/{id}', 'MeetingController@edit')->name('meetingedit.edit');
Route::put('/meeting/update/{id}', 'MeetingController@update')->name('meetingupdate.update');
Route::get('/deletemeeting/{id}', 'MeetingController@destroy')->name('deletemeeting.destroy');
Route::get('/meeting/show/{id}', 'MeetingController@show')->name('meeting.show');

Route::get('doc/show/{id}', 'MeetingDocumentController@show')->name('doc.show.json');
Route::get('meeting_document/create', 'MeetingDocumentController@create')->name('createmeetingdocument.create');
Route::post('meeting_document/store', 'MeetingDocumentController@store')->name('storemeetingdocument.store');
Route::get('/meeting_document/edit/{id}', 'MeetingDocumentController@edit')->name('meetingdocedit.edit');
Route::put('/meeting_document/update/{id}', 'MeetingDocumentController@update')->name('meetingdocupdate.update');
Route::get('/meetingdocdelete/{id}', 'MeetingDocumentController@destroy')->name('meetingdocdelete.destroy');
Route::get('doc/show2/{id}', 'MeetingDocumentController@show_2')->name('doc.show');