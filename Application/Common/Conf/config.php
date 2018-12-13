<?php
$config = array(
    'PWD_ENCRYPT_STR' => 'youngtxxx',
    'CSS_VER'         => 1,
    'JS_VER'          => 1,
    //'SHOW_PAGE_TRACE' => true,              // 显示页面Trace信息
    'SHOW_ERROR_MSG'  => true,
    'COOKIE_PREFIX'   => 'tm_',
    'COOKIE_EXPIRE'   => 86400 * 7,
    'COOKIE_PATH'     => '/',
    //'COOKIE_DOMAIN'   => 'sale.com',
    //'配置项'=>'配置值'
    'SESSION_OPTIONS' => array(//'domain' => 'sale.com',
    ),
    /* URL设置 */
    'URL_MODEL'       => 2,                  //URL模式


    'MODULE_ALLOW_LIST' => array('Home', 'Admin', 'Data', 'BiHu', 'HaiTao', 'Fxg'),
    'DEFAULT_MODULE'    => 'Home',

    'DATA_CACHE_PREFIX'     => 'TM_',//缓存前缀
    'DATA_CACHE_TIME'       => 604800,//缓存时间
    'DATA_CACHE_TYPE'       => 'Redis',//默认动态缓存为Redis
    'REDIS_HOST'            => '47.96.78.2', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    'REDIS_PORT'            => '6379',//端口号
    'REDIS_AUTH'            => 'redis-pass',//AUTH认证密码

    /** 线上配置 */
    //    'REDIS_HOST'            => 'r-bp1526b5feedcee4.redis.rds.aliyuncs.com', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    //    'REDIS_PORT'            => '6379',//端口号
    //    'REDIS_AUTH'            => 'Sdwl2017',//AUTH认证密码
    //

    //url 区分大小写
    'URL_CASE_INSENSITIVE'  => false,
    /* 子域名配置 */
    'APP_SUB_DOMAIN_DEPLOY' => 1,             // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'  => array(
        'admin'  => 'Admin',
        'sale'   => 'Admin',
        'data'   => 'Data',
        'bihu'   => 'BiHu',
        'haitao' => 'HaiTao',
        'fxg'    => 'Fxg',
        'tm'     => 'Article'
    ),
    'IS_AUTO_SEND_TOP'      => 0,//头条专辑自动发文
    'IS_AUTO_SEND_WEI_TOP'  => 0,//微头条自动发文
    'TB_ACCESS_TOKEN'       => array(
        'default_token' => '7000210092878546cc7e2ac63b7e2b13f4a6a2f11ec5116adfa5e8a3c648f894709cb4d3199378718',
    ),
);
return $config;

