<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 下午 3:49
 */
$config = array(
    'DB_TYPE'   => 'mysql',
    //线上数据库
    'DB_HOST'   => 'rm-bp14074suyvwpe33f.mysql.rds.aliyuncs.com',
    'DB_NAME'   => 'temai',
    'DB_USER'   => 'taodianke',
    'DB_PWD'    => 'taodianke@163_com',
    'DB_PREFIX' => 'ytt_',
    'FXG_DB'    => array(
        'DB_TYPE'   => 'mysql',
        //线上数据库
        'DB_HOST'   => 'rm-bp14074suyvwpe33f.mysql.rds.aliyuncs.com',
        'DB_NAME'   => 'haitao',
        'DB_USER'   => 'taodianke',
        'DB_PWD'    => 'taodianke@163_com',
        'DB_PREFIX' => 'fxg_',
    )
);
return $config;