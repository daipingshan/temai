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
            
    <fieldset class="layui-elem-field">
        <legend>保存文章</legend>
        <div class="layui-field-box" style="min-width: 590px;">
            <fieldset class="layui-elem-field">
                <legend>机器选品</legend>
                <div class="layui-field-box">
                    <form class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">
                                关键字
                            </label>
                            <div class="layui-input-block">
                                <input type="text" name="keyword" class="layui-input"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">商品分类</label>
                            <div class="layui-input-block">
                                <input type="radio" name="media_id" value="" title="全部" checked>
                                <?php $cate = C('ARTICLE_CATE'); ?>
                                <?php if(is_array($cate)): foreach($cate as $k=>$row): ?><input type="radio" name="media_id" value="<?php echo ($k); ?>" title="<?php echo ($row); ?>"><?php endforeach; endif; ?>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">
                                商品数量
                            </label>
                            <div class="layui-input-inline">
                                <input type="number" name="num" value="15" class="layui-input"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">
                            </label>
                            <button class="layui-btn" lay-submit lay-filter="get-item" type="submit">
                                获取前三个商品
                            </button>
                            <button class="layui-btn" lay-submit lay-filter="get-more-item" type="submit">
                                获取更多商品
                            </button>
                        </div>
                    </form>
                </div>
            </fieldset>
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">发布账号</label>
                    <div class="layui-input-inline">
                        <select name="account_id" id="account">
                            <option value="0">请选择发布账号</option>
                            <?php if(is_array($account)): $i = 0; $__LIST__ = $account;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><option value='<?php echo ($row["id"]); ?>'><?php echo ($row["username"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">文章标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" id="title" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属领域</label>
                    <div class="layui-input-inline">
                        <select name="classify" id="classify" class="form-control">
                            <option value="0">请选择</option>
                            <?php if(is_array($classify_data)): foreach($classify_data as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <?php if(($group_id) == "1"): ?><div class="layui-form-item">
                        <label class="layui-form-label">文章类型</label>
                        <div class="layui-input-block">
                            <input type="radio" name="news_type" value="1" title="图集" checked>
                            <input type="radio" name="news_type" value="2" title="专辑">
                        </div>
                    </div><?php endif; ?>
                <div class="layui-row">
                    <div id="ajax-content"></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="save" type="submit">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>

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

    <script type="text/javascript">
        form.render();
        var index;
        var goods_id = [];
        var num = 0;
        var key = 1;
        //监听提交
        form.on('submit(get-item)', function (data) {
            num = 0;
            key = 1;
            goods_id = [];
            $('#ajax-content').parent().html('<div id="ajax-content"></div>');
            if (data.field.num < 3) {
                layer.msg('商品数量不能小于3！');
                return false;
            }
            index = layer.msg('查询中，请稍候', {icon: 16, time: false, shade: 0.8});
            $.get("<?php echo U('Sale/getShopItemId');?>", data.field, function (res) {
                layer.close(index);
                if (res.status == 1) {
                    goods_id = JSON.parse(res.info);
                    getItemData(3);
                } else {
                    layer.msg(res.info);
                }
            });
            return false;
        });

        //监听提交
        form.on('submit(get-more-item)', function () {
            var count = goods_id.length;
            if (count == 0) {
                layer.msg('请先获取前三条数据再获取更多数据！');
            } else {
                if (key >= count) {
                    layer.msg('数据已全部加载完成！');
                } else {
                    num++;
                    key++;
                    getItemData(count)
                }
            }
            return false;
        });

        form.on('submit(save)', function (data) {
            var _this = $(this);
            if (data.field.account_id == 0) {
                layer.msg('请选择文章发布账号！');
                return false;
            }
            if (!data.field.title) {
                layer.msg('请输入文章标题！');
                return false;
            }
            if (data.field.classify == 0) {
                layer.msg('请选择文章所属领域！');
                return false;
            }
            _this.addClass('layui-disabled');
            var url = "<?php echo U('Sale/saveShopNews');?>";
            _this.text('正在保存中...');
            $.post(url, data.field, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    setTimeout(function () {
                        window.parent.location.href = "<?php echo U('Sale/newsList');?>";
                    }, 3000)
                } else {
                    _this.text('保存');
                    _this.removeClass('layui-disabled');
                }
            })
            return false
        });

        /**
         * 获取商品数据
         */
        function getItemData(i) {
            index = layer.msg('第' + key + '个商品正在生成中......', {icon: 16, time: false, shade: 0.8});
            $.post("<?php echo U('Sale/getShopItem');?>", {shop_goods_id: goods_id[num], num: key}, function (res) {
                layer.close(index);
                if (res.status == 1) {
                    $('#ajax-content').before(res.info);
                } else {
                    layer.msg('第' + num + 1 + '个商品' + res.info);
                }
                if (key >= i) {
                    layer.msg('数据加载完成');
                } else {
                    num++;
                    key++;
                    getItemData(i);
                }
            })
        }

        $('body').on('click', '.del-item', function () {
            $(this).parents('.shop-item').remove();
        })

        $('body').on('mouseover', '.img', function () {
            $(this).find('.img-vice').show();
        });
        $('body').on('mouseout', '.img', function () {
            $(this).find('.img-vice').hide();
        });

    </script>

</body>
</html>