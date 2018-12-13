<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>特卖管理后台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/Public/Admin/css/font.css?v=<?php echo C('CSS_VER');?>">
    <link rel="stylesheet" href="/Public/Admin/css/xadmin.css?v=<?php echo C('CSS_VER');?>">
    <link rel="stylesheet" href="/Public/Admin/css/swiper.min.css?v=<?php echo C('CSS_VER');?>">
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js?v=<?php echo C('JS_VER');?>"></script>
    <script type="text/javascript"
            src="/Public/Admin/js/swiper.jquery.min.js?v=<?php echo C('JS_VER');?>"></script>
    <script src="/Public/Admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/xadmin.js"></script>

</head>
<body>
<div class="login-logo"><h1>特卖管理后台</h1></div>
<div class="login-box">
    <form class="layui-form layui-form-pane" action="">

        <h3>登录你的帐号</h3>
        <label class="login-title" for="username">帐号</label>
        <div class="layui-form-item">
            <label class="layui-form-label login-form" style="width:56px;"><i class="iconfont">&#xe6b8;</i></label>
            <div class="layui-input-inline login-inline">
                <input type="text" name="username" placeholder="请输入你的帐号" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <label class="login-title" for="password">密码</label>
        <div class="layui-form-item">
            <label class="layui-form-label login-form" style="width:56px;"><i class="iconfont">&#xe82b;</i></label>
            <div class="layui-input-inline login-inline">
                <input type="password" name="password" placeholder="请输入你的密码" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-warning pull-right" lay-submit lay-filter="login" type="submit">登录</button>
        </div>
    </form>
</div>
<div class="bg-changer">
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
<script>
    $(function () {
        layui.use(['form', 'layer'], function () {
            var form = layui.form;
            var layer = layui.layer;
            //监听提交
            form.on('submit(login)', function (data) {
                if (!data.field.username) {
                    layer.msg('请输入用户名！');
                    return false;
                }
                if (!data.field.password) {
                    layer.msg('请输入密码！');
                    return false;
                }
                $.post("<?php echo U('doLogin');?>", data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            location.href = "<?php echo U('Index/index');?>"
                        });
                    } else {
                        layer.msg(res.info);
                    }
                });
                return false;
            });
        });
    })
</script>
</body>
</html>