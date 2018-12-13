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
            
    <?php if(($group_id) == "1"): ?><form class="layui-form xbs" action="">
            <div class="layui-form-pane">
                <div class="layui-input-inline">
                    <select name="user_id">
                        <option value="0">请选择用户</option>
                        <?php if(is_array($user_data)): foreach($user_data as $k=>$row): ?><option value="<?php echo ($row["id"]); ?>"
                            <?php if(I('get.user_id') == $row['id']): ?>selected<?php endif; ?>
                            ><?php echo ($row["name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="news_type">
                        <option value="0">请选择类型</option>
                        <option value="1"
                        <?php if(I('get.news_type') == 1): ?>selected<?php endif; ?>
                        >图集</option>
                        <option value="2"
                        <?php if(I('get.news_type') == 2): ?>selected<?php endif; ?>
                        >专辑</option>
                    </select>
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <button class="layui-btn" style="background-color: rgba(0,0,0,0.4)"><i class="layui-icon">
                        &#xe615;</i></button>
                </div>
            </div>
        </form><?php endif; ?>
    <table class="layui-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col width="300">
        </colgroup>
        <thead>
        <tr>
            <th>文章标题</th>
            <th>文章数量</th>
            <th>文章领域</th>
            <th>文章类型</th>
            <th>发布账号</th>
            <th>发布人</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
                    <?php if(($vo['is_send'] == 0) AND ($vo['is_save'] == 0)): ?><a href="javascript:;" class="update-news-info" data-id="<?php echo ($vo["id"]); ?>" data-title="<?php echo ($vo["title"]); ?>"
                           style="color: #fff"><?php echo ($vo["title"]); ?></a>
                        <?php else: ?>
                        <a href="javascript:;" class="news-info"
                           data-url="<?php echo U('Sale/newsInfo',array('id'=>$vo['id']));?>"
                           style="color: #fff"
                           data-title="预览文章"
                           onclick="x_admin_show($(this).data('title'),$(this).data('url'),'50%','95%')"><?php echo ($vo["title"]); ?></a><?php endif; ?>
                </td>
                <td>
                    <?php echo count(json_decode($vo['json_content'])); ?>
                </td>
                <td><?php echo ($classify_data[$vo['classify']]); ?></td>
                <td>
                    <?php if(($vo["news_type"]) == "1"): ?><div class="layui-btn layui-btn-xs layui-disabled">图集</div>
                        <?php else: ?>
                        <div class="layui-btn layui-btn-normal layui-btn-xs layui-disabled">专辑</div><?php endif; ?>
                </td>
                <td><?php echo ($account[$vo['account_id']]['username']); ?></td>
                <td><?php echo ($vo["username"]); ?></td>
                <td>
                    <?php if($vo['send_time']): echo ($vo["send_time"]); ?><br>
                        <?php else: ?>
                        未设置定时发布<br><?php endif; ?>
                    <?php echo (date("Y-m-d H:i:s",$vo["add_time"])); ?>
                </td>
                <td>
                    <?php if($vo['is_send'] == 0): if(($vo['is_save']) == "1"): ?><a class="layui-btn layui-btn-sm layui-disabled">已存稿</a>
                            <?php else: ?>
                            <a class="layui-btn layui-btn-sm figure"
                               data-url="<?php echo U('Sale/figure',array('id'=>$vo['id']));?>"
                               href="javascript:;">存草稿</a><?php endif; endif; ?>
                <!--    <?php if(($group_id) == "1"): ?><a class="layui-btn layui-btn-sm layui-btn-normal add-cart" href="javascript:;"
                           data-url="<?php echo U('copyCart',array('id'=>$vo['id']));?>">加入选品库</a><?php endif; ?>-->
                    <a class="layui-btn layui-btn-sm layui-btn-normal add-cart" href="javascript:;"
                       data-url="<?php echo U('copyCart',array('id'=>$vo['id']));?>">加入选品库</a>
                    <?php if($vo['news_type'] == 1): ?><a class="layui-btn layui-btn-sm layui-btn-warm news-info" href="javascript:;"
                           data-url="<?php echo U('Sale/newsInfo',array('id'=>$vo['id']));?>"
                           data-title="预览文章"
                           onclick="x_admin_show($(this).data('title'),$(this).data('url'),'50%','95%')">预览</a>
                        <?php else: ?>
                        <a class="layui-btn layui-btn-sm layui-btn-warm"
                           href="<?php echo U('Sale/updateArticleTitle',array('id'=>$vo['id']));?>">
                            <?php if(($vo['is_save'] == 1) OR ($vo['is_send'] == 1)): ?>查看
                                <?php else: ?>
                                编辑<?php endif; ?>
                        </a><?php endif; ?>
                    <?php if(($vo['is_send'] == 0) AND ($vo['is_save'] == 0)): ?><a class="layui-btn layui-btn-sm layui-btn-danger news-del" href="javascript:;"
                           data-url="<?php echo U('Sale/newsDel',array('id'=>$vo['id']));?>">删除</a><?php endif; ?>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    <?php echo ($page); ?>

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

    <div id="update-title-box" style="display: none;padding:20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">文章标题</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入文章标题" id="update-title" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <input type="hidden" name="id" id="news-id"/>
                    <button class="layui-btn" id="save-title" type="button">修改</button>
                </div>
            </div>
        </form>
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
        $('.update-news-info').click(function () {
            var _this = $(this);
            $('#news-id').val(_this.data('id'));
            $('#update-title').val(_this.data('title'));
            layer.open({
                type: 1,
                area: ['50%', '40%'],
                fix: false, //不固定
                maxmin: true,
                shadeClose: true,
                shade: 0.4,
                title: '修改文章标题',
                content: $('#update-title-box'),
                cancel: function () {
                    $('#update-title-box').hide();
                }
            });
        })

        $('#save-title').click(function () {
            var _this = $(this);
            var title = $('#update-title').val();
            var id = $('#news-id').val();
            if (!title) {
                layer.msg('请输入文章标题');
                return false;
            }
            _this.addClass('layui-disabled');
            $.post("<?php echo U('TopLine/updateNews');?>", {id: id, title: title}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(res.info);
                    _this.removeClass('layui-disabled');
                }
            });
        })
        $('.figure').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章存草稿吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                } else {
                    is_click = 0;
                    _this.addClass('layui-disabled');
                    $.get(url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, function () {
                                window.location.reload();
                            })
                        } else {
                            is_click = 1;
                            layer.msg(res.info);
                            _this.removeClass('layui-disabled');
                        }
                    })
                }
            });
        });
        $('.add-cart').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章中的商品加入选品库吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                    return false;
                } else {
                    is_click = 0;
                    _this.addClass('layui-disabled');
                    $.get(url, {}, function (res) {
                        layer.msg(res.info);
                        is_click = 1;
                        _this.removeClass('layui-disabled');
                    })
                }
            });
        })
        $('.publish').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章发布吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                } else {
                    is_click = 0;
                    _this.addClass('layui-disabled');
                    $.get(url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, function () {
                                window.location.reload();
                            })
                        } else {
                            is_click = 1;
                            layer.msg(res.info);
                            _this.removeClass('layui-disabled');
                        }
                    })
                }
            });
        })
        $('.news-del').click(function () {
            var is_click = 1;
            var _this = $(this);
            var url = $(this).data('url');
            layer.confirm('确认要将此文章删除吗？', {
                btn: ['确定', '取消']//按钮
            }, function () {
                if (is_click == 0) {
                    layer.msg('正在请求中……，请勿重复点击！');
                    return false;
                } else {
                    is_click = 0;
                    _this.addClass('layui-disabled');
                    $.get(url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, function () {
                                window.location.reload();
                            })
                        } else {
                            is_click = 1;
                            layer.msg(res.info);
                            _this.removeClass('layui-disabled');
                        }
                    })
                }
            });
        })
    </script>

</body>
</html>