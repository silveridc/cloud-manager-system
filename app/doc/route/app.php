<?php
use think\facade\Route;

Route::get('index', 'DocController/index');
Route::get('search', 'DocController/search');
Route::get('list', "DocController/getList");
Route::get('pass', "DocController/pass");
Route::post('login', "DocController/login");
Route::get('info', "DocController/getInfo");