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
            
    <form class="layui-form xbs" action="<?php echo U('TopLine/weiTopLine');?>" name="search">
        <div class="layui-form-pane">
            <div class="layui-form-item" style="display: inline-block;">
                <div class="layui-input-inline">
                    <input type="text" name="shop_goods_id" placeholder="请输入商品编号" autocomplete="off"
                           class="layui-input" value="<?php echo I('get.shop_goods_id');?>">
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <button class="layui-btn" style="background-color: rgba(0,0,0,0.4)"><i class="layui-icon">
                        &#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <?php if(!empty($img_data)): ?><xblock>
            <form class="layui-form xbs">
                <?php if(is_array($img_data)): $i = 0; $__LIST__ = $img_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="float: left;width: 15%;margin-right: 5%;position: relative;margin-top: 10px">
                        <img src="<?php echo ($vo); ?>" alt="" style="width: 100%;border:1px solid red">
                        <div style="position: absolute;top: 10px;right: 10px">
                            <input type="checkbox" name="img[]" title="选择图片" value="<?php echo ($vo); ?>">
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <div style="clear: both;margin-bottom: 20px"></div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <select lay-filter="content">
                            <option value="0">请选择内容</option>
                            <?php if(is_array($desc_data)): $i = 0; $__LIST__ = $desc_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$desc): $mod = ($i % 2 );++$i;?><option value="<?php echo ($desc); ?>"><?php echo ($desc); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">内容</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入内容" class="layui-textarea" name="content"></textarea>
                    </div>
                </div>
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
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="save" type="submit">一键发送</button>
                    </div>
                </div>
            </form>
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
        var index;
        form.render();
        var shop_goods_id = "<?php echo I('get.shop_goods_id');?>";
        var source_id = "<?php echo ($source_id); ?>";
        var url = "https://haohuo.snssdk.com/views/product/item?id=" + shop_goods_id + "&source_type=4&source_id=" + source_id + "&origin_type=4&origin_id=" + source_id + "_" + shop_goods_id + "&tt_project_id=6#tt_daymode=1&tt_font=m"
        form.on('select(content)', function (data) {
            $('textarea').val(data.value + ' 商品链接【' + url + '】');
        });
        form.on('submit(save)', function (data) {
            index = layer.msg('正在发送请稍侯......', {icon: 16, time: false, shade: 0.8});
            $.post("<?php echo U('sendWeiTopLine');?>", data.field, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        location.href = "<?php echo U('weiTopLine');?>";
                    })
                } else {
                    layer.msg(res.info);
                }
            })
            return false;
        })
    </script>

</body>
</html>