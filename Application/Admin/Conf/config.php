<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 14:37
 */

return array(
    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__'    => '/Public/Admin/js',
        '__CSS_PATH__'   => '/Public/Admin/css',
        '__IMAGE_PATH__' => '/Public/Admin/images',
        '__FONT_PATH__'  => '/Public/Admin/fonts',
        '__LAYUI_PATH__' => '/Public/Admin/lib/layui',
    ),
    'LOAD_EXT_CONFIG'   => 'db,token,auth',
    'ARTICLE_CATE'      => array(
        '5500950648' => '户外推荐',
        '5500838410' => '生活美食会',
        '5500803015' => '数码周刊',
        '5500367585' => '美妆丽人',
        '5501814762' => '酷奇潮玩',
        '5501679303' => '宝妈推荐',
        '5500311915' => '懂车品',
        '5500358214' => '每日搭配之道',
        '5500903267' => '放心家居',
        '5501832587' => '潮男指南',
        '6768458493' => '放心购精选'
    ),
    'SEND_TOP_LINE_CONFIG'=> array(
        'custom'=>array(
            array(
                    'user_id'=>array(),
                    'text'   =>'喜欢本图片商品可以点击下面蓝色字体购买',
                    'desc'   => true,
                ),
            array(
                    'user_id'=>array(),
                    'text'   =>'喜欢本图片商品可以点击下面蓝色字体购买',
                    'desc'   => false,
                )
        ),
        'default'=>array(
            'text'=>'如果喜欢本图片单击蓝色文字了解',
            'desc'=>true,
        )
    ),
);