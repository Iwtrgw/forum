<?php

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
    return view('welcome');
});

Auth::routes();

Route::view('scan','scan');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('threads','ThreadController@index')->name('threads');
Route::get('threads/create','ThreadController@create');
Route::get('threads/{channel}/{thread}','ThreadController@show');

Route::get('threads/search','SearchController@show');
// 话题更新
Route::patch('threads/{channel}/{thread}','ThreadController@update');
Route::delete('threads/{channel}/{thread}','ThreadController@destroy');
Route::post('threads','ThreadController@store')->middleware('must-be-confirmed');
Route::get('threads/{channel}','ThreadController@index');

Route::post('locked-threads/{thread}','LockedThreadsController@store')->name('locked-threads.store')->middleware('admin'); // 锁定话题
Route::delete('locked-threads/{thread}','LockedThreadsController@destroy')->name('locked-threads.destroy')->middleware('admin'); // 解锁话题


Route::get('/threads/{channel}/{thread}/replies','ReplyController@index');
Route::post('/threads/{channel}/{thread}/replies','ReplyController@store');

// 最佳回复
Route::post('/replies/{reply}/best','BestRepliesController@store')->name('best-replies.store');

Route::patch('/replies/{reply}','ReplyController@update');
Route::delete('/replies/{reply}','ReplyController@destroy')->name('replies.destroy'); // 删除最佳回复


Route::post('/replies/{reply}/favorites','FavoritesController@store');
Route::delete('/replies/{reply}/favorites','FavoritesController@destroy'); // 取消点赞

// 话题订阅
Route::post('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@destroy')->middleware('auth');

// 个人中心路由
Route::get('/profiles/{user}','ProfilesController@show')->name('profile');

Route::get('/profiles/{user}/notifications','UserNotificationsController@index'); // 订阅通知消息
Route::delete('/profiles/{user}/notifications/{notification}','UserNotificationsController@destroy'); // 清除已读通知消息

Route::get('/register/confirm','Auth\RegisterConfirmationController@index')->name('register.confirm'); // 用户注册邮件认证

// @ 时搜索用户名的集合
Route::get('api/users','Api\UsersController@index');
// 图像上传
Route::post('api/users/{user}/avatar','Api\UserAvatarController@store')->middleware('auth')->name('avatar');