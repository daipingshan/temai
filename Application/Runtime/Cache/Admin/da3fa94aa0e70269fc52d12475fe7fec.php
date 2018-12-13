<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>特卖管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/font.css">
    <link rel="stylesheet" href="/Public/Admin/css/xadmin.css?v=<?php echo C('CSS_VER');?>">
    <link rel="stylesheet" href="/Public/Admin/css/swiper.min.css">
    
    <link rel="stylesheet" href="/Public/Admin/css/item_box.css">

    <script src="/Public/Admin/lib/layui/layui.all.js" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/swiper.jquery.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/xadmin.js?v=<?php echo C('JS_VER');?>"></script>
</head>
<body>
<!-- 顶部开始 -->
<div class="container" style="position: fixed;top: 0;height: 70px;z-index:10;">
    <div class="logo"><a href="<?php echo U('Index/index');?>" style="color: #fff">特卖管理</a></div>
    <div class="open-nav"><i class="iconfont">&#xe699;</i></div>
    <ul class="layui-nav right" style="padding: 5px 20px;background:none">
        <li class="layui-nav-item" style="line-height: 65px">
            <a href="javascript:;"><?php echo session('admin_user_info')['name'];?><span class="layui-nav-more"></span></a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a href="javascript:;"
                       data-title="修改密码"
                       data-url="<?php echo U('User/updatePass');?>"
                       onclick="x_admin_show($(this).data('title'),$(this).data('url'),'40%','35%')">修改密码</a>
                </dd>
                <?php if(($group_id) == "1"): ?><a data-url="<?php echo U('Item/updateItemId');?>" class="get-last-id" style="cursor: pointer">更新商品ID</a><?php endif; ?>
                <dd><a href="<?php echo U('Login/logout');?>">退出</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item" style="line-height: 65px;padding: 0 30px;cursor: pointer"
            id="full-screen" data-status="0">
            <i class="icon iconfont" style="font-size: 20px;color: #fff;">&#xe6fd;</i>
        </li>
    </ul>
</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<div class="wrapper" style="position: relative;margin-top:70px;margin-bottom: 50px">
    <!-- 左侧菜单开始 -->
    <div class="left-nav">
        <div id="side-nav">
            <ul id="nav">
                <?php if(is_array($menu_list)): $i = 0; $__LIST__ = $menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i; if($row['son_menu']): ?><li class="list">
                            <a href="javascript:;">
                                <i class="iconfont">&#xe70b;</i>
                                <?php echo ($row["menu_name"]); ?>
                                <i class="iconfont nav_right">&#xe697;</i>
                            </a>
                            <ul class="sub-menu">
                                <?php if(is_array($row["son_menu"])): $i = 0; $__LIST__ = $row["son_menu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$son): $mod = ($i % 2 );++$i;?><li>
                                        <a href="<?php echo U($son['url']);?>"
                                        <?php if(($son['url'] == 'Article/index') OR ($son['url'] == 'Article/articleInfo')): ?>target="_blank"<?php endif; ?>
                                        data-type="2">
                                        <i class="iconfont">&#xe6a7;</i>
                                        <?php echo ($son["menu_name"]); ?>
                                        </a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li class="list">
                            <a href="<?php echo U($row['url']);?>"
                               data-type="1"
                                <i class="iconfont">&#xe70b;</i>
                                <?php echo ($row["menu_name"]); ?>
                            </a>
                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!-- 左侧菜单结束 -->
    <!-- 右侧主体开始 -->
    <div class="page-content">
        <div class="content">
            
    <form class="layui-form xbs" action="">
        <div class="layui-form-pane">
            <div class="layui-form-item" style="display: inline-block;">
                <div class="layui-input-inline" style="width: 400px">
                    <input type="text" name="time" placeholder="请选择时间范围" autocomplete="off"
                           class="layui-input" id="time" value="<?php echo ($time); ?>">
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <input type="hidden" name="id" value="<?php echo I('get.id');?>">
                    <button class="layui-btn" style="background-color: rgba(0,0,0,0.4)"><i class="layui-icon">
                        &#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <?php if(!empty($data)): ?><fieldset class="layui-elem-field">
            <legend>文章标题</legend>
            <div class="layui-field-box">
                <?php echo ($article_title); ?><span style="color: #4bffe8">【总业绩：<?php echo ($money); ?>】</span>
            </div>
        </fieldset>
        <xblock>
            <div class="layui-row">
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="layui-row">
                        <?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
                                <div class="item-box">
                                    <div class="img">
                                        <img lay-src="<?php echo ($row["img"]); ?>">
                                        <div class="img-vice">
                                            <img lay-src="<?php echo ($row["img_vice"]); ?>">
                                        </div>
                                        <div class="intro">
                                            <a href="https://haohuo.snssdk.com/views/product/item?id=<?php echo ($row["shop_goods_id"]); ?>"
                                               target="_blank" style="color: #fff"><p><?php echo ($row["description"]); ?></p></a>
                                        </div>
                                    </div>
                                    <div class="item-content">
                                        <a href="https://haohuo.snssdk.com/views/product/item?id=<?php echo ($row["shop_goods_id"]); ?>"
                                           target="_blank">
                                            <p class="article-title"
                                               style="height: 50px;line-height: 25px;font-size: 14px">
                                                <?php echo ($row["description_vice"]); ?></p>
                                        </a>
                                        <div class="layui-row float-box">
                                            <div class="layui-col-xs5">
                                                <div class="price">
                                                    <b>￥<?php echo ($row["price"]); ?></b>
                                                </div>
                                            </div>
                                            <div class="layui-col-xs7">
                                                <div class="right commission-detail" style="cursor: pointer">
                                                    订单收益详情？
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-row float-box">
                                            <div class="layui-col-xs6">
                                                <div class="center">
                                                    成交笔数:<?php echo ($row["goods_num"]); ?>
                                                </div>
                                            </div>
                                            <div class="layui-col-xs6">
                                                <div class="center">
                                                    总收益：<?php echo ($row["goods_fee"]); ?>元
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($group_id == 1): ?><div class="layui-row float-box">
                                                <?php if(($row["is_add"]) == "1"): ?><button class="layui-btn">已添加至选品库</button>
                                                    <?php else: ?>
                                                    <div class="layui-col-xs6">
                                                        <button class="layui-btn default-bgcolor addItemCache"
                                                                data-id="<?php echo ($row["shop_goods_id"]); ?>"
                                                                data-url="<?php echo U('Sale/addItemCache');?>"
                                                                data-val='<?php echo ($row["post_data"]); ?>'
                                                                data-type="0">
                                                            一键添加
                                                        </button>
                                                    </div>
                                                    <div class="layui-col-xs6">
                                                        <button class="layui-btn default-bgcolor addItemCache"
                                                                data-id="<?php echo ($row["shop_goods_id"]); ?>"
                                                                data-url="<?php echo U('Sale/addItemCache');?>"
                                                                data-val='<?php echo ($row["post_data"]); ?>'
                                                                data-type="1">
                                                            伪原创
                                                        </button>
                                                    </div><?php endif; ?>
                                            </div><?php endif; ?>
                                    </div>
                                    <div class="commission-info">
                                        <div class="layui-row" style="margin: 10px"><?php echo ($row["data_content"]); ?></div>
                                    </div>
                                </div>
                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </xblock><?php endif; ?>

        </div>
    </div>
    <!-- 右侧主体结束 -->
</div>
<!-- 中部结束 -->
<!-- 底部开始 -->
<div class="footer" style="position: fixed;bottom: 0;line-height: 50px;text-align: center;height: 50px;z-index:10;">
    <div class="copyright">Copyright ©2017-2018 dps v1.0 All Rights Reserved. 本后台系统由dps提供技术支持</div>
</div>

<div id="back-to-top"
     style="position: fixed;bottom: 0; right:0;line-height: 50px;text-align: center;height: 50px;z-index:12;width: 50px;cursor: pointer;display: none">
    <i class="layui-icon" style="font-size: 30px; color: #fff;">&#xe604;</i>
</div>
<!-- 底部结束 -->
<!-- 背景切换开始 -->
<div class="bg-changer" style="position: fixed;z-index:11">
    <div class="swiper-container changer-list">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/a.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/b.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/c.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/d.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/e.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/f.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/g.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/h.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/i.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/j.jpg" alt=""></div>
            <div class="swiper-slide"><img class="item" src="/Public/Admin/images/k.jpg" alt=""></div>
            <div class="swiper-slide"><span class="reset">初始化</span></div>
        </div>
    </div>
    <div class="bg-out"></div>
    <div id="changer-set"><i class="iconfont">&#xe696;</i></div>
</div>

    <div class="layui-row"
     style="position: fixed;bottom: 0;z-index:11;background: rgba(0,0,0,0.6);height: 50px;width: 100%">
    <div class="layui-col-xs3">
        <div id="cart" style="position: relative;width: 50px;height: 40px;line-height: 40px;margin: 5px auto">
            <i class="layui-icon temai-preview" style="color: #fff;font-size: 40px;cursor: pointer"
               data-url="<?php echo U('cartList');?>" data-title="预览文章" data-width="50%" data-height="95%">&#xe657;</i>
            <span class="layui-badge cart-num" style="position: absolute;top:5px;right: 0"><?php echo ($cart_count); ?></span>
        </div>
    </div>
    <div class=" layui-col-xs3" style="text-align: center;line-height: 50px">
        <button class="layui-btn temai-preview" data-title="保存文章" data-width="80%" data-height="95%"
                data-url="<?php echo U('saveCart');?>">
            一键保存
        </button>
    </div>
    <div class="layui-col-xs3" style="text-align: center;line-height: 50px">
        <button class="layui-btn layui-btn-danger temai-preview" data-title="预览文章" data-width="50%" data-height="95%"
                data-url="<?php echo U('cartList');?>">
            一键预览
        </button>
    </div>
    <div class="layui-col-xs3" style="text-align: center;line-height: 50px">
    </div>
</div>

<script type="text/javascript">
    var layer = layui.layer,
        element = layui.element,
        form = layui.form,
        laydate = layui.laydate,
        upload = layui.upload,
        carousel = layui.carousel,
        flow = layui.flow,
        util = layui.util,
        table = layui.table,
        laypage = layui.laypage,
        laytpl = layui.laytpl,
        layedit = layui.layedit;

    $(function () {
        $(window).scroll(function () {
            if ($(window).scrollTop() > 100) {
                $("#back-to-top").fadeIn(1500);
            } else {
                $("#back-to-top").fadeOut(1500);
            }
        });
        //当点击跳转链接后，回到页面顶部位置
        $("#back-to-top").click(function () {
            $('body,html').animate({scrollTop: 0}, 500);
            return false;
        });
        var location_url = window.location.href;
        $('#nav a').each(function () {
            if ($(this).attr('href') != 'javascript:;') {
                var url_arr = $(this).attr('href').split('.');
                if (location_url.indexOf(url_arr[0]) != -1) {
                    if ($(this).data('type') == 1) {
                        $(this).parent().addClass('current');
                    } else {
                        $(this).parents('li.list').addClass('open');
                        $(this).parents('ul.sub-menu').addClass('opened');
                        $(this).parent().addClass('current');
                    }
                }
            }
        });
        $('.get-last-id').click(function () {
            $.get($(this).data('url'));
            layer.msg('更新成功');
        })
    });
</script>
<!-- 背景切换结束 -->

    <script type="text/javascript" src="/Public/Admin/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/top_line_item.js"></script>
    <script type="text/javascript">
        form.render();
        //日期时间范围
        laydate.render({
            elem: '#time'
            , type: 'date'
            , range: '~'
        });
        flow.lazyimg();
        $(".commission-detail").hover(function () {
            $(this).parents('.item-box').find('.commission-info').show();
        }, function () {
            $(this).parents('.item-box').find('.commission-info').hide();
        });
        $(".img").hover(function () {
            $(this).find('.img-vice').show();
        }, function () {
            $(this).find('.img-vice').hide();
        });
    </script>

</body>
</html>