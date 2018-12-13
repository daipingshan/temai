<?php
return array(
    'AUTH_LIST' => array(
        1  => array(
            'name' => '超级管理员',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '用户管理', 'url' => 'User/index'),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '订单列表', 'url' => 'Order/index'),
                    array('menu_name' => '认领订单', 'url' => 'Order/claim'),
                    array('menu_name' => '订单统计', 'url' => 'Order/count'),
                    array('menu_name' => '业绩排行', 'url' => 'Order/achievement'),
                    array('menu_name' => '奖励排行', 'url' => 'Order/rewardRank'),
                    array('menu_name' => '订单抓取', 'url' => 'Order/orderCollection'),
                    array('menu_name' => '我的订单', 'url' => 'Order/myOrder'),
                    array('menu_name' => '我的奖励', 'url' => 'Order/myReward'),
                    array('menu_name' => '失效订单', 'url' => 'Order/invalidOrder'),
                    array('menu_name' => '订单分析', 'url' => 'Order/orderCount'),
                )),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                    array('menu_name' => '文章统计', 'url' => 'News/count'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '高佣金商品', 'url' => 'Item/commissionList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励选品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardItem'),
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                    array('menu_name' => '添加收藏', 'url' => 'Item/collect'),
                    array('menu_name' => '我的收藏', 'url' => 'Item/userCollect'),
                )),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '账号管理', 'url' => 'TopLine/index'),
                    array('menu_name' => '选品管理', 'url' => 'TopLine/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                    array('menu_name' => '专辑文章', 'url' => 'TopLine/albumList'),
                    array('menu_name' => '微头条', 'url' => 'TopLine/daTaoKeItemsList'),
                    array('menu_name' => '新微头条', 'url' => 'TopLine/weiTopLine'),
                    array('menu_name' => '添加机器选品', 'url' => 'TopLine/goodsList'),
                    array('menu_name' => '机器选品', 'url' => 'TopLine/machine'),
                    array('menu_name' => '小店选品', 'url' => 'TopLine/shopList'),
                    array('menu_name' => '小店文章', 'url' => 'TopLine/shopNewsList'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '账号管理', 'url' => 'Sale/index'),
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '收藏选品', 'url' => 'Sale/collectList'),
                    array('menu_name' => '机器选品', 'url' => 'Sale/machine'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '小店管理', 'son_menu' => array(
                    array('menu_name' => '小店账号', 'url' => 'Shop/index'),
                    array('menu_name' => '商品抓取', 'url' => 'Shop/itemCollection'),
                    array('menu_name' => '选品同步', 'url' => 'Shop/shopItemCollection'),
                    array('menu_name' => '添加推荐语', 'url' => 'Shop/product'),
                    array('menu_name' => '推荐语管理', 'url' => 'Shop/userItems'),
                    array('menu_name' => '审核推荐语', 'url' => 'Shop/userItemsList'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '文章管理', 'url' => 'SaleArticle/index'),
                    array('menu_name' => '我的文章', 'url' => 'SaleArticle/userArticle'),
                    array('menu_name' => '历史文章', 'url' => 'SaleArticle/historyArticle'),
                    array('menu_name' => '文章走势', 'url' => 'Article/index'),
                    array('menu_name' => '文章分析', 'url' => 'Article/articleInfo'),
                    array('menu_name' => '藏金阁文章', 'url' => 'Article/taoJinGeArticle'),
                )),
            )
        ),
        2  => array(
            'name' => '文案组长',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '订单列表', 'url' => 'Order/index'),
                    array('menu_name' => '认领订单', 'url' => 'Order/claim'),
                    array('menu_name' => '订单统计', 'url' => 'Order/count'),
                    array('menu_name' => '我的订单', 'url' => 'Order/myOrder'),
                    array('menu_name' => '我的奖励', 'url' => 'Order/myReward'),
                    array('menu_name' => '失效订单', 'url' => 'Order/invalidOrder'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                    array('menu_name' => '收藏选品', 'url' => 'Sale/collectList'),
                )),
                array('menu_name' => '小店管理', 'son_menu' => array(
                    array('menu_name' => '添加推荐语', 'url' => 'Shop/product'),
                    array('menu_name' => '推荐语管理', 'url' => 'Shop/userItems'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '我的文章', 'url' => 'SaleArticle/userArticle'),
                    array('menu_name' => '历史文章', 'url' => 'SaleArticle/historyArticle'),
                    array('menu_name' => '文章走势', 'url' => 'Article/index'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '高佣金商品', 'url' => 'Item/commissionList'),
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                    array('menu_name' => '添加收藏', 'url' => 'Item/collect'),
                    array('menu_name' => '我的收藏', 'url' => 'Item/userCollect'),
                )),
            )
        ),
        3  => array(
            'name' => '文案合作',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                )),
            )
        ),
        4  => array(
            'name' => '头条发文',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '选品管理', 'url' => 'TopLine/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                )),
            )
        ),
        5  => array(
            'name' => '放心购上单',
            'data' => array(
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '文章走势', 'url' => 'Article/index'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                )),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '小店选品', 'url' => 'TopLine/shopList'),
                    array('menu_name' => '小店文章', 'url' => 'TopLine/shopNewsList'),
                )),
            ),

        ),
        6  => array(
            'name' => '果果专用',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '选品管理', 'url' => 'TopLine/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                )),
            )
        ),
        7  => array(
            'name' => '公司小店发文',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '订单列表', 'url' => 'Order/index'),
                    array('menu_name' => '认领订单', 'url' => 'Order/claim'),
                    array('menu_name' => '订单统计', 'url' => 'Order/count'),
                    array('menu_name' => '我的订单', 'url' => 'Order/myOrder'),
                    array('menu_name' => '我的奖励', 'url' => 'Order/myReward'),
                    array('menu_name' => '失效订单', 'url' => 'Order/invalidOrder'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '小店管理', 'son_menu' => array(
                    array('menu_name' => '添加推荐语', 'url' => 'Shop/product'),
                    array('menu_name' => '推荐语管理', 'url' => 'Shop/userItems'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '我的文章', 'url' => 'SaleArticle/userArticle'),
                    array('menu_name' => '历史文章', 'url' => 'SaleArticle/historyArticle'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '高佣金商品', 'url' => 'Item/commissionList'),
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                )),
            )
        ),
        8  => array(
            'name' => '文案合作1',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                )),
            )
        ),
        9  => array(
            'name' => '机器发文',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '订单列表', 'url' => 'Order/index'),
                    array('menu_name' => '认领订单', 'url' => 'Order/claim'),
                    array('menu_name' => '订单统计', 'url' => 'Order/count'),
                    array('menu_name' => '我的订单', 'url' => 'Order/myOrder'),
                    array('menu_name' => '我的奖励', 'url' => 'Order/myReward'),
                    array('menu_name' => '失效订单', 'url' => 'Order/invalidOrder'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '机器选品', 'url' => 'Sale/machine'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                )),
                array('menu_name' => '小店管理', 'son_menu' => array(
                    array('menu_name' => '添加推荐语', 'url' => 'Shop/product'),
                    array('menu_name' => '推荐语管理', 'url' => 'Shop/userItems'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '我的文章', 'url' => 'SaleArticle/userArticle'),
                    array('menu_name' => '历史文章', 'url' => 'SaleArticle/historyArticle'),
                    array('menu_name' => '文章走势', 'url' => 'Article/index'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '高佣金商品', 'url' => 'Item/commissionList'),
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                    array('menu_name' => '添加收藏', 'url' => 'Item/collect'),
                    array('menu_name' => '我的收藏', 'url' => 'Item/userCollect'),
                )),
            )
        ),
        10 => array(
            'name' => '林萍小店发文',
            'data' => array(
                array('menu_name' => '后台主页', 'url' => 'Index/index'),
                array('menu_name' => '文章管理', 'son_menu' => array(
                    array('menu_name' => '文章列表', 'url' => 'News/index'),
                    array('menu_name' => '登记文章', 'url' => 'News/add'),
                )),
                array('menu_name' => '订单管理', 'son_menu' => array(
                    array('menu_name' => '订单列表', 'url' => 'Order/index'),
                    array('menu_name' => '认领订单', 'url' => 'Order/claim'),
                    array('menu_name' => '订单统计', 'url' => 'Order/count'),
                    array('menu_name' => '我的订单', 'url' => 'Order/myOrder'),
                    array('menu_name' => '我的奖励', 'url' => 'Order/myReward'),
                    array('menu_name' => '失效订单', 'url' => 'Order/invalidOrder'),
                )),
                array('menu_name' => '今日头条', 'son_menu' => array(
                    array('menu_name' => '选品管理', 'url' => 'TopLine/itemsList'),
                    array('menu_name' => '链接选品', 'url' => 'TopLine/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'TopLine/newsList'),
                )),
                array('menu_name' => '特卖发文', 'son_menu' => array(
                    array('menu_name' => '非商品图集', 'url' => 'Sale/imgList'),
                    array('menu_name' => '选品管理', 'url' => 'Sale/itemsList'),
                    array('menu_name' => '小店选品', 'url' => 'Sale/shopList'),
                    array('menu_name' => '推荐商品', 'url' => 'Sale/tuijianList'),
                    array('menu_name' => '链接选品', 'url' => 'Sale/itemsUrlList'),
                    array('menu_name' => '文章管理', 'url' => 'Sale/newsList'),
                    array('menu_name' => '收藏选品', 'url' => 'Sale/collectList'),
                )),
                array('menu_name' => '小店管理', 'son_menu' => array(
                    array('menu_name' => '添加推荐语', 'url' => 'Shop/product'),
                    array('menu_name' => '推荐语管理', 'url' => 'Shop/userItems'),
                )),
                array('menu_name' => '文章抓取', 'son_menu' => array(
                    array('menu_name' => '文章统计', 'url' => 'SaleArticle/count'),
                    array('menu_name' => '我的文章', 'url' => 'SaleArticle/userArticle'),
                    array('menu_name' => '历史文章', 'url' => 'SaleArticle/historyArticle'),
                    array('menu_name' => '文章走势', 'url' => 'Article/index'),
                )),
                array('menu_name' => '商品管理', 'son_menu' => array(
                    array('menu_name' => '高佣金商品', 'url' => 'Item/commissionList'),
                    array('menu_name' => '公司出单商品', 'url' => 'Item/orderItemsList'),
                    array('menu_name' => '特卖商品', 'url' => 'Item/saleList'),
                    array('menu_name' => '奖励商品', 'url' => 'Item/rewardList'),
                    array('menu_name' => '商品统计', 'url' => 'Item/count'),
                    array('menu_name' => '暴文首图', 'url' => 'Item/topImg'),
                    array('menu_name' => '添加收藏', 'url' => 'Item/collect'),
                    array('menu_name' => '我的收藏', 'url' => 'Item/userCollect'),
                )),
            )
        ),
    )
);