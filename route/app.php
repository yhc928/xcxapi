<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// 获取当前计数
Route::get('/api/count', 'index/getCount');

// 更新计数，自增或者清零
Route::post('/api/count', 'index/updateCount');
Route::rule('login','Login/login');
Route::rule('article','Article/index');
Route::rule('articles','Article/save');
Route::rule('articled','Article/delete');
Route::rule('articleu','Article/update');
//上传富文本编辑器中的图片
Route::rule('upload','UploadImg/upload');
//文章中的封面图片
Route::rule('cover','Cover/upload');
//小程序前端使用
Route::rule('wxarticle','Article/readList');
Route::rule('wxdetail','Article/readDetail');
Route::rule('wxtuijian','Article/readtuijian');


Route::rule('access_token','Cover/getaccess_token');
