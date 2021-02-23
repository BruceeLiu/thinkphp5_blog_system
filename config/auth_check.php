<?php

return [
    //模块名
    'admin'=>[
        //控制器名
        'login'=>[
            //方法名
            'login'=>[
                'method'=>'GET',
                'is_check' => false
            ],

            'defaultlogin'=>[
                'method'=>'POST',
                'is_check' => false
            ],

            'loginout'=>[
                'method'=>'GET',
                'is_check' => false
            ],
            'refreshtoken'=>[
                'method'=>'GET',
                'is_check' => false
            ],
            'verify'=>[
                'method'=>'GET',
                'is_check' => false
            ]
        ],
        //后台首页
        'index'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ]

        ],
        //管理员
        'adminuser' => [
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'adduser'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'isuse'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //权限管理
        'auths' => [
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addauth'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ]
        ],
        //角色管理
        'roles'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addroles'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'isuse'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //文章管理
        'articlemanages'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addarticle'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'audit'=>[
                'method'=>'POST',
                'is_check' => true
            ]
        ],
        //标签管理
        'articletags'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addtags'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'isuse'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //分类管理
        'articletypes'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addtypes'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'isuse'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //评论管理
        'articlecomment' => [
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //日志
        'systemlog'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ]
        ],
        //系统配置
        'systemsconfig' => [
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'update' => [
                'method'=>'POST',
                'is_check' => true
            ]
        ],
        //网站发展时间线
        'networkline' => [
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addline'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'POST',
                'is_check' => true
            ]
        ],
        //友情链接
        'link'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'detail'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'add'=>[
                'method'=>'GET',
                'is_check' => true
            ],
            'addlink'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'update'=>[
                'method'=>'POST',
                'is_check' => true
            ],
            'del'=>[
                'method'=>'POST',
                'is_check' => true
            ]
        ]
    ],
    'index'=>[
        'index'=>[
            'index'=>[
                'method'=>'GET',
                'is_check' => false
            ]
        ]
    ]
];