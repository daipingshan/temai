<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>海淘管理后台</title>
    <link rel="stylesheet" href="/Public/HaiTao/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/HaiTao/css/common.css">
    
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <ul class="layui-nav layui-layout-left" style="left: 0">
            <li class="layui-nav-item <?php if(($controller_name) == "Index"): ?>active<?php endif; ?>"><a
                    href="<?php echo U('Index/index');?>" class="first">控制台</a></li>
            <?php if(session('fxg_user_info')['is_super'] == 1): ?><li class="layui-nav-item <?php if(($controller_name) == "User"): ?>active<?php endif; ?>"><a class="first"
                                                                                                  href="<?php echo U('User/index');?>">管理员管理</a>
                </li>
                <li class="layui-nav-item <?php if(($controller_name) == "Shop"): ?>active<?php endif; ?>">
                    <a href="javascript:" class="first">店铺管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?php echo U('Shop/index');?>">店铺列表</a></dd>
                        <dd><a href="<?php echo U('Shop/add');?>">添加店铺</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item <?php if(($controller_name) == "Logistic"): ?>active<?php endif; ?>"><a class="first"
                                                                                                      href="<?php echo U('Logistics/index');?>">快递管理</a>
                </li><?php endif; ?>
            <li class="layui-nav-item <?php if(($controller_name) == "Item"): ?>active<?php endif; ?>"><a class="first"
                                                                                              href="<?php echo U('Item/index');?>">商品管理</a>
            </li>
            <li class="layui-nav-item <?php if(($controller_name) == "Order"): ?>active<?php endif; ?>"><a class="first"
                                                                                               href="<?php echo U('Order/index');?>">订单管理</a>
            </li>

            <?php if(session('fxg_user_info')['is_super'] == 1): ?><li class="layui-nav-item <?php if(($controller_name) == "Finance"): ?>active<?php endif; ?>"><a class="first"
                                                                                                     href="<?php echo U('Finance/index');?>">财务管理</a>
                </li><?php endif; ?>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                    <?php echo session('fxg_user_info')['username'];?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;" onclick="updatePass()">修改密码</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="<?php echo U('Login/logout');?>">退了</a></li>
        </ul>
    </div>
    <div class="layui-body" style="left: 0;padding: 20px">
        
    <form class="layui-form search" action="" name="search">
        <div class="layui-row">
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="text" name="shop_name" value="<?php echo I('get.shop_name');?>" placeholder="请输入店铺名称"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
                <a class="layui-btn layui-btn-danger" href="<?php echo U('add');?>"><i class="layui-icon">
                    &#xe654;</i>添加店铺
                </a>
            </div>
        </div>
    </form>
    <table class="layui-hide" id="table" lay-filter="table"></table>

    </div>
    <div class="layui-footer" style="left: 0; text-align: center">
        © Copyright ©2017-2018 dps v1.0 All Rights Reserved. 本后台系统由dps提供技术支持
    </div>
</div>
<div class="hide" style="padding: 20px" id="update-pass">
    <form class="layui-form" action="" name="update-pass">
        <div class="layui-form-item">
            <label class="layui-form-label">登录密码</label>
            <div class="layui-input-block">
                <input type="text" placeholder="请输入登录密码" name="password" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
                <input type="text" placeholder="请输入确认密码" name="pass" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="update-pass">修改</button>
            </div>
        </div>
    </form>
</div>
<script src="/Public/HaiTao/lib/layui/layui.all.js"></script>
<script>
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
        layedit = layui.layedit,
        $ = layui.jquery;

    function updatePass() {
        $('#update-pass input').val('');
        layer.open({
            type: 1,
            title: '修改密码',
            area: ['500px', '260px'],
            content: $('#update-pass')
        })
    }

    //监听提交
    form.on('submit(update-pass)', function (data) {
        if (!data.field.password) {
            layer.msg('请输入登录密码！');
            return false;
        }
        if (!data.field.pass) {
            layer.msg('请输入确认密码！');
            return false;
        }
        if (data.field.password != data.field.pass) {
            layer.msg('两次密码不一致！');
            return false;
        }
        $.post("<?php echo U('Index/updatePass');?>", data.field, function (res) {
            if (res.status == 1) {
                layer.msg(res.info, function () {
                    parent.layer.closeAll();
                });
            } else {
                layer.msg(res.info);
            }
        });
        return false;
    });

</script>


    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs" href="<?php echo U('update');?>?id={{d.id}}">编辑</a>
    </script>
    <script>
        $(function () {
            var index;
            var url = "<?php echo U('index');?>";
            getData(url);
            $('form.search').on('click', 'button.search', function () {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            });

            function getData(url) {
                table.render({
                    elem: '#table'
                    , url: url
                    , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
                        layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                        , groups: 5 //只显示 1 个连续页码
                    }
                    , cellMinWidth: 80
                    , cols: [[
                        {field: 'id', title: 'ID', sort: true}
                        , {field: 'shop_name', width: 200, title: '店铺名称'}
                        , {field: 'app_key', title: 'app_key'}
                        , {field: 'app_secret', width: 300, title: 'app_secret'}
                        , {field: 'create_time', title: '添加时间'}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit'}
                    ]]
                });
            }
        })
    </script>

</body>
</html>