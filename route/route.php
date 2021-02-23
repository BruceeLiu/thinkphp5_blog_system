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

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

//登录
Route::group('admin/login',function (){
    Route::get('login','login');
    Route::get('out','loginOut');
    Route::post('check','defaultLogin');
    Route::post('refresh/token','refreshToken');
    Route::get('captcha','verify');
})->prefix('admin/Login/')->ext('html');

//首页
Route::group('admin/index',function (){
    Route::get('index','index');
})->prefix('admin/Index/')->ext('html');

//管理员
Route::group('admin/user',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addUser');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::get('is_use','isUse');
    Route::get('delete','del');
})->prefix('admin/AdminUser/')->ext('html');

//权限
Route::group('admin/auth',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addAuth');
    Route::post('update','update');
    Route::get('detail','detail');
})->prefix('admin/Auths/')->ext('html');

//角色
Route::group('admin/role',function (){
    Route::get('index','index');
    Route::get('detail','detail');
    Route::get('add','add');
    Route::post('create','addRoles');
    Route::post('update','update');
    Route::get('delete','del');
    Route::get('is_use','isUse');
})->prefix('admin/Roles/')->ext('html');

//文章
Route::group('admin/article',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addArticle');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::get('delete','del');
    Route::post('audit','audit');
})->prefix('admin/ArticleManages/')->ext('html');

//标签
Route::group('article/tags',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addTags');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::get('is_use','isUse');
    Route::get('delete','del');
})->prefix('admin/ArticleTags/')->ext('html');

//分类
Route::group('article/type',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addTypes');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::get('is_use','isUse');
    Route::get('delete','del');
})->prefix('admin/ArticleTypes/')->ext('html');

//评论管理
Route::group('article/comment',function (){
    Route::get('index','index');
    Route::get('delete','del');
})->prefix('admin/ArticleComment/')->ext('html');

Route::group('front',function (){
    Route::group('index',function (){
        Route::get('index','index');
        Route::get('detail','detail');
    })->prefix('front/Index/')->ext('html');

    Route::group('abouts',function (){
        Route::get('index','index');
    })->prefix('front/AboutUs/')->ext('html');

    Route::group('covid',function (){
        Route::get('index','index');
        Route::get('detail','situations');
    })->prefix('front/CovidSituation/');

    Route::group('article',function (){
        Route::get('index','index');
        Route::get('select_type','accordArticleType');
    })->prefix('front/Article/');
    Route::group('line',function (){
        Route::get('index','index');
    })->prefix('front/NetworkTimeLine/');
});

Route::group('system',function (){
    Route::group('log',function (){
        Route::get('lists','index');
    })->prefix('admin/SystemLog/');
    Route::group('config',function (){
        Route::get('index','index');
        Route::post('update','update');
    })->prefix('admin/SystemsConfig/');
});

Route::group('admin/lines',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addLine');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::post('delete','del');
})->prefix('admin/NetworkLine/')->ext('html');

Route::group('admin/links',function (){
    Route::get('index','index');
    Route::get('add','add');
    Route::post('create','addLink');
    Route::post('update','update');
    Route::get('detail','detail');
    Route::post('delete','del');
})->prefix('admin/Link/')->ext('html');