<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>放心购管理后台</title>
    <link rel="stylesheet" href="/Public/HaiTao/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/HaiTao/css/common.css">
    
    <style>
        .layui-form-select .layui-edge {
            right: 25%;
        }
    </style>

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
        
    <form class="layui-form search" action="" name="search">
        <div class="layui-row">
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <select name="shop_id">
                    <option value="">请选择店铺</option>
                    <?php if(is_array($shop_data)): foreach($shop_data as $key=>$row): ?><option value="<?php echo ($row["id"]); ?>"><?php echo ($row["shop_name"]); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="pay_time" value="<?php echo I('get.pay_time');?>" id="pay_time" placeholder="请选择下单时间"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="receipt_time" value="<?php echo I('get.receipt_time');?>" id="receipt_time"
                       placeholder="请选择收货时间"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md4">
                <input type="checkbox" name="data_status" title="异常" class="layui-input">
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
                <button type="button" class="layui-btn layui-btn-danger" id="upload-amount"><i class="layui-icon"></i>批量设置结算金额
                </button>
                <button class="layui-btn layui-btn-normal down" type="button">下载
                </button>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2" style="line-height: 50px">
                总利润：<span id="money" style="color: red">0</span>元
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
        {{d.income}}|{{d.money}}
    </script>
    <script type="text/html" id="order">
        <a target="_blank"
           href="<?php echo U('Order/index');?>?order_id={{d.order_id}}">{{d.order_id}}</a>
    </script>
    <script>
        $(function () {
            var tableObj;
            form.render();
            laydate.render({
                elem: '#pay_time'
                , type: 'date'
                , range:
                    '~'
            });
            laydate.render({
                elem: '#receipt_time'
                , type: 'date'
                , range:
                    '~'
            });
            var url = "<?php echo U('settle');?>";
            getData(url);
            $('form.search').on('click', 'button.search', function () {
                var pay_time = $('input[name=pay_time]').val();
                var receipt_time = $('input[name=receipt_time]').val();
                if (pay_time == '' && receipt_time == '') {
                    layer.msg('请选择下单时间或收货时间后再查询！');
                    return false;
                }
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            });

            $('form.search').on('click', 'button.down', function () {
                var pay_time = $('input[name=pay_time]').val();
                var receipt_time = $('input[name=receipt_time]').val();
                if (pay_time == '' && receipt_time == '') {
                    layer.msg('请选择下单时间或收货时间后再下载！');
                    return false;
                }
                var down_url = "<?php echo U('settleDownData');?>";
                var param = $('form.search').serialize();
                down_url = down_url + '?' + param;
                location.href = down_url;
            });

            upload.render({
                elem: '#upload-amount'
                , url: "<?php echo U('Finance/setSettleAmount');?>"
                , accept: 'file'
                , exts: 'xlsx|xls|docx|csv'
                , field: 'filename'
                , before: function () {
                    layer.msg('处理中，请稍候', {icon: 16, time: false, shade: 0.8});
                }
                , done: function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            tableObj.reload();
                        })
                    } else {
                        layer.msg(res.info);
                    }
                }
            });

            function getData(url) {
                tableObj = table.render({
                    elem: '#table'
                    , url: url
                    , page: false
                    , height: 'full-230'
                    , cellMinWidth: 100
                    , cols: [[
                        {field: 'order_id', width: 180, title: '订单编号', sort: true, templet: '#order'}
                        , {field: 'shop_name', title: '店铺名称'}
                        , {field: 'product_name', title: '商品信息', width: 200}
                        , {field: 'total_amount', title: '付款金额'}
                        , {field: 'create_time_view', title: '下单时间', width: 170, sort: true}
                        , {field: 'receipt_time', title: '收货时间', width: 170, sort: true}
                        , {field: 'order_status_view', title: '订单状态',width: 80}
                        , {field: 'order_status_remark', title: '数据状态',width: 80}
                        , {field: 'product_pay_amount', title: '拍单价',width: 80}
                        , {field: 'commission', title: '达人服务费',width: 80}
                        , {field: 'settle_amount', title: '结算金额',width: 80}
                        , {fixed: 'right', field: 'money', toolbar: '#table-edit', title: '利润', width: 120}
                    ]]
                    , done: function (res, curr, count) {
                        $('#money').text(count);
                    }
                });
            }
        })
    </script>

</body>
</html>