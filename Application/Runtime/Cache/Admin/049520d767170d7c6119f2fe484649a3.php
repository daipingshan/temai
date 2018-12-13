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
            
    <form class="layui-form xbs" action="<?php echo U('SaleArticle/index');?>" name="search">
        <div class="layui-form-pane">
            <div class="layui-form-item" style="display: inline-block;">
                <div class="layui-input-inline">
                    <input type="text" name="keyword" placeholder="请输入商品关键字" autocomplete="off"
                           class="layui-input" value="<?php echo I('get.keyword');?>">
                </div>
                <div class="layui-input-inline" style="width: 100px">
                    <input type="text" name="read_num" placeholder="阅读量大于" autocomplete="off"
                           class="layui-input" value="<?php echo I('get.read_num');?>">
                </div>
                <div class="layui-input-inline" style="width:100px;">
                    <input type="number" name="create_user_id" placeholder="作者ID" autocomplete="off"
                           class="layui-input" value="<?php echo I('get.create_user_id');?>">
                </div>
                <div class="layui-input-inline" style="width: 300px">
                    <input type="text" name="time" class="layui-input" placeholder="请选择时间范围" id="time"
                           value="<?php echo I('get.time','','trim,urldecode');?>">
                </div>
                <div class="layui-input-inline">
                    <select name="sort">
                        <option value="0">请选择排序方式</option>
                        <option value="1"
                        <?php if(I('get.sort') == 1): ?>selected<?php endif; ?>
                        >阅读量从高到低</option>
                        <option value="2"
                        <?php if(I('get.sort') == 2): ?>selected<?php endif; ?>
                        >评论量从高到低</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="article_genre">
                        <option value="0">请选择平台</option>
                        <option value="1"
                        <?php if(I('get.article_genre') == 1): ?>selected<?php endif; ?>
                        >图集</option>
                        <option value="2"
                        <?php if(I('get.article_genre') == 2): ?>selected<?php endif; ?>
                        >专辑</option>
                    </select>
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <input type="hidden" name="media_id" id="media_id" value="<?php echo I('get.media_id');?>">
                    <button class="layui-btn" style="background-color: rgba(0,0,0,0.4)"><i class="layui-icon">
                        &#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <xblock>
        <?php $cate = C('ARTICLE_CATE'); ?>
        <button data-id="" class="layui-btn layui-btn-sm item-cate"
        <?php if(I('get.media_id')): ?>style="background:rgba(0,0,0,0.4)"<?php endif; ?>
        >全部</button>
        <?php if(is_array($cate)): foreach($cate as $k=>$row): ?><button data-id="<?php echo ($k); ?>" class="layui-btn layui-btn-sm item-cate"
            <?php if(I('get.media_id') != $k): ?>style="background:rgba(0,0,0,0.4)"<?php endif; ?>
            ><?php echo ($row); ?></button><?php endforeach; endif; ?>
    </xblock>
    <table class="layui-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>头条标题</th>
            <th>阅读量</th>
            <th>评论量</th>
            <th>分类</th>
            <th>平台</th>
            <th>作者ID</th>
            <th>发布时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
                    <a href="https://www.toutiao.com/a<?php echo ($vo["article_id"]); ?>" target="_blank"
                       style="color: #fff"><?php echo ($vo["title"]); ?></a>
                </td>
                <td><?php echo ($vo["go_detail_count"]); ?></td>
                <td><?php echo ($vo["comments_count"]); ?></td>
                <td><?php echo ($cate[$vo['media_id']]); ?></td>
                <td>
                    <?php if($vo['article_genre'] == 1): ?><button class="layui-btn layui-btn-sm layui-btn-normal layui-disabled">图集</button>
                        <?php else: ?>
                        <button class="layui-btn layui-btn-danger layui-btn-sm layui-disabled">专辑</button><?php endif; ?>
                </td>
                <td>
                    <?php if($vo['userid'] == 1): ?><p style="color:red;"><?php echo ($vo["create_user_id"]); ?>【<?php echo ($vo["num"]); ?>】</p>
                        <?php else: ?>
                        <?php echo ($vo["create_user_id"]); ?>【<?php echo ($vo["num"]); ?>】<?php endif; ?>

                </td>
                <td>
                    <?php echo (date("Y-m-d H:i:s",$vo["behot_time"])); ?>
                </td>
                <td>
                    <button class="layui-btn layui-btn-sm" style="background:rgba(0,0,0,0.4)"
                            data-url="<?php echo U('TopLine/openArticleDetail',array('id'=>$vo['id']));?>"
                            onclick="window.open($(this).data('url'))">查看商品
                    </button>
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
    <script>
        form.render();
        //日期时间范围
        var nowTime = new Date().valueOf();
        var time = laydate.render({
            elem: '#time'
            , type: 'datetime'
            , range: '~'
            , max: nowTime
        });
        $('.item-cate').click(function () {
            $('#media_id').val($(this).data('id'));
            $('form[name=search]').submit();
        });
    </script>

</body>
</html>