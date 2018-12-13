<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>放心购管理后台</title>
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
                </li>
                <li class="layui-nav-item <?php if(($controller_name) == "OrderError"): ?>active<?php endif; ?>"><a class="first"
                                                                                                        href="<?php echo U('OrderError/index');?>">订单失败原因</a>
                </li><?php endif; ?>
            <li class="layui-nav-item <?php if(($controller_name) == "Item"): ?>active<?php endif; ?>"><a class="first"
                                                                                              href="<?php echo U('Item/index');?>">商品管理</a>
            </li>
            <li class="layui-nav-item <?php if(($controller_name) == "Order"): ?>active<?php endif; ?>"><a class="first"
                                                                                               href="<?php echo U('Order/index');?>">订单管理</a>
            </li>

            <?php if(session('fxg_user_info')['is_super'] == 1): ?><li class="layui-nav-item <?php if(($controller_name) == "Finance"): ?>active<?php endif; ?>">
                    <a href="javascript:" class="first">财务管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?php echo U('Finance/index');?>">预估收入</a></dd>
                        <dd><a href="<?php echo U('Finance/settle');?>">结算收入</a></dd>
                    </dl>
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
        
    <form class="layui-form xbs" action="<?php echo U('User/index');?>">
        <div class="layui-form-pane">
            <div class="layui-form-item" style="display: inline-block;">
                <div class="layui-input-inline">
                    <input type="text" name="username" placeholder="请输入用户名" autocomplete="off"
                           class="layui-input" value="<?php echo I('get.username');?>">
                </div>
                <div class="layui-input-inline">
                    <button class="layui-btn"><i class="layui-icon">
                        &#xe615;</i></button>
                    <button class="layui-btn" type="button"
                            onclick="saveUser('添加用户','<?php echo U('User/add');?>','600','300')"><i
                            class="layui-icon">&#xe608;</i>添加
                    </button>
                </div>
            </div>
        </div>
    </form>
    <table class="layui-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>账号名称</th>
            <th>操作权限</th>
            <th>添加时间</th>
            <th>设置授权</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td><?php echo ($vo["username"]); ?></td>
                <td>
                    <?php if(($vo["is_super"]) == "1"): ?>超级权限
                        <?php else: ?>
                        普通权限<?php endif; ?>
                </td>
                <td><?php echo (date("Y-m-d H:i",$vo["create_time"])); ?></td>
                <td>
                    <button class="layui-btn layui-btn-sm"
                            data-url="<?php echo U('User/auth',array('id'=>$vo['id'],'type'=>'shop'));?>"
                            onclick="saveUser('店铺授权',$(this).data('url'),'600','200')"
                            title="店铺授权">
                        <i class="layui-icon" style="font-size: 20px;color: #fff">&#xe631;</i>店铺授权
                    </button>
                </td>
                <td>
                    <a title="编辑" href="javascript:;" data-url="<?php echo U('User/update',array('id'=>$vo['id']));?>"
                       onclick="saveUser('编辑',$(this).data('url'),'600','300')"
                       class="layui-btn layui-btn-xs">
                        <i class="layui-icon">&#xe642;</i>编辑
                    </a>
                    <a title="删除" href="javascript:;" onclick="member_del(<?php echo ($vo['id']); ?>)"
                       class="layui-btn-xs layui-btn layui-btn-danger">
                        <i class="layui-icon">&#xe640;</i>删除
                    </a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    <?php echo ($page); ?>

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


    <script type="text/javascript">
        form.render();

        /*用户-删除*/
        function member_del(id) {
            layer.confirm('确认要删除该用户吗？', {title: "删除确认"}, function (index) {
                $.post("<?php echo U('User/deleteAccount');?>", {id: id}, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.info);
                    }
                });
            });
        }

        function saveUser(title, url, w, h) {
            var area = [w + 'px', h + 'px'];
            layer.open({
                type: 2,
                area: area,
                fix: false, //不固定
                shadeClose: true,
                shade: 0.4,
                title: title,
                content: url
            });
        }
    </script>

</body>
</html>