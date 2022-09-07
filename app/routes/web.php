<?php

use Kernel\Auth\UserRoles;
use \Kernel\Route;

Route\Route::add('get', '/', 'IndexController', 'index');
Route\Route::add('get', '/404', 'Error404Controller', 'index');
Route\Route::add('get', '/watch', 'WatchController', 'index');


Route\Route::add('get', '/get-part', 'GetVideoPartController', 'index');


// login
Route\Route::add('post', '/login', 'LoginController', 'index');


// signup
Route\Route::add('post', '/signup', 'RegisterController', 'index');


// upload-video
Route\Route::add('get', '/upload-video', 'UploadVideoController', 'index');
Route\Route::add('post', '/upload-video', 'UploadVideoController', 'upload');


// edit-video
Route\Route::add('get', '/edit-video', 'EditVideoController', 'index');
Route\Route::add('get', '/edit-video/{video_id}', 'EditVideoController', 'edit');
Route\Route::add('post', '/edit-video', 'EditVideoController', 'save');


Route\Route::add('get', '/confirm', 'ConfirmController', 'index');

// test
Route\Route::add('get', '/test', 'TestController', 'index');