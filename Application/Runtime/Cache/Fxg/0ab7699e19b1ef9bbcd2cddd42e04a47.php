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
                <input type="text" name="order_id" value="<?php echo I('get.order_id');?>" placeholder="请输入订单编号"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="product_keyword" value="<?php echo I('get.product_keyword');?>" placeholder="请输入商品ID或标题"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <select name="order_source">
                    <option value="">请选择订单来源</option>
                    <?php if(is_array($order_source)): foreach($order_source as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="post_data" value="<?php echo I('get.post_data');?>" placeholder="请输入收货人或电话"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <select name="logistics_id">
                    <option value="">全部</option>
                    <?php if(is_array($logistics)): foreach($logistics as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row["name"]); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="logistics_code" value="<?php echo I('get.logistics_code');?>" placeholder="请输入快递单号"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <select class="form-control" name="pay_type">
                    <option value="">请选择支付方式</option>
                    <?php if(is_array($pay_type)): foreach($pay_type as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="pay_time" value="<?php echo I('get.pay_time');?>" id="pay_time" placeholder="请选择下单时间"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md6">
                <input type="checkbox" name="data_status" value="1" title="异常">
                <input type="hidden" name="order_status" value="0"/>
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
                <button type="button" class="layui-btn layui-btn-warm" id="upload-amount"><i
                        class="layui-icon"></i>批量设置阿里单号及拍单价
                </button>
                <button type="button" class="layui-btn layui-btn-danger" id="upload"><i class="layui-icon"></i>批量发货
                </button>
                <button class="layui-btn layui-btn-normal down" type="button">下载
                </button>
            </div>
        </div>
    </form>
    <div class="layui-tab layui-tab-card">
        <ul class="layui-tab-title search-li">
            <li data-id="0" class="layui-this">全部</li>
            <li data-id="1">待确认</li>
            <li data-id="2">备货中</li>
            <li data-id="3">已发货</li>
            <li data-id="4">已取消</li>
            <li data-id="5">已完成</li>
            <li data-id="6">退货</li>
            <li data-id="7">退款</li>
            <li data-id="9">退货失败</li>
        </ul>
    </div>
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

    <div id="set-order" class="hide" style="padding: 20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label" id="label"></label>
                <div class="layui-input-block w50">
                    <input type="text" placeholder="请输入" name="order_field_value" id="order-field-value"
                           class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <input type="hidden" name="order_id" id="order-id" value="">
                <input type="hidden" name="order_field" id="order-field" value="">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="set-order" type="submit">提交</button>
                </div>
            </div>
        </form>
    </div>
    <div id="set-logistics" class="hide" style="padding: 20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">快递代码</label>
                <div class="layui-input-block w50">
                    <input type="text" placeholder="请输入快递代码" name="logistics_english_name"
                           class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">快递单号</label>
                <div class="layui-input-block w50">
                    <input type="text" placeholder="请输入快递单号" name="logistics_code"
                           class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <input type="hidden" name="order_id" value="">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="set-logistics" type="submit">提交</button>
                </div>
            </div>
        </form>
    </div>


    <script type="text/html" id="img">
        <!-- 这里的 checked 的状态只是演示 -->
        <div class="open-img-layer cursor"><img layer-src={{d.product_pic}} src="{{d.product_pic}}"
                                                alt="{{d.product_name}}"></div>
    </script>
    <script type="text/html" id="product">
        <a data-id="{{d.product_id}}" target="_blank"
           href="https://haohuo.snssdk.com/views/product/item?id={{d.product_id}}">{{d.product_name}}</a>
    </script>
    <script type="text/html" id="table-edit">
        {{#  if( d.order_status == 2){ }}
        {{#  if(d.ali_order_id == ''){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal set-order" data-id="{{d.id}}" data-label="阿里订单号"
           data-field="ali_order_id">设置阿里订单号</a>
        {{#  }else{ }}
        <a class="layui-btn layui-btn-xs set-order" data-id="{{d.id}}" data-val="{{d.ali_order_id}}"
           data-label="阿里订单号"
           data-field="ali_order_id">修改阿里订单号</a>
        {{#  } }}
        {{#  } }}
        {{#  if(d.is_set == 1 ){ }}
        {{#  if(d.product_pay_amount == 0 ){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal set-order" data-id="{{d.id}}" data-label="拍单价"
           data-field="product_pay_amount">设置拍单价</a>
        {{#  }else{ }}
        <a class="layui-btn layui-btn-xs set-order" data-id="{{d.id}}"
           data-val="{{d.product_pay_amount}}" data-label="拍单价"
           data-field="product_pay_amount">修改拍单价</a>
        {{#  } }}
        {{#  } }}
        {{#  if(d.pay_type == 0 && d.order_status == 1 ){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal order-confirm" data-id="{{d.id}}"
           data-field="product_pay_amount">订单确认</a>
        {{#  } }}

        {{#  if(d.order_status == 3 ){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal set-logistics" data-id="{{d.id}}"
           data-field="product_pay_amount">重新发货</a>
        {{#  } }}

    </script>
    <script>
        $(function () {
            form.render();
            var index;
            var tableObj;
            laydate.render({
                elem: '#pay_time'
                , type: 'date'
                , range: '~'
            });
            $('body').on('mouseover', '.open-img-layer', function () {
                var _this = $(this);
                layer.photos({
                    photos: _this
                });
            })

            upload.render({
                elem: '#upload'
                , url: "<?php echo U('Order/setOrderStatus');?>"
                , accept: 'file'
                , exts: 'xlsx|xls|docx'
                , field: 'filename'
                , before: function () {
                    layer.msg('处理中，请稍候', {icon: 16, time: false, shade: 0.8});
                }
                , done: function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {time: 8000}, function () {
                            tableObj.reload();
                        })
                    } else {
                        layer.msg(res.info);
                    }
                }
            });
            upload.render({
                elem: '#upload-amount'
                , url: "<?php echo U('Order/setProductAmount');?>"
                , accept: 'file'
                , exts: 'xlsx|xls|docx'
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
            var url = "<?php echo U('index');?>";
            var param = $('form.search').serialize();
            var get_url = url + '?' + param;
            getData(get_url);

            $('form.search').on('click', 'button.search', function () {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            });

            $('form.search').on('click', 'button.down', function () {
                var down_url = "<?php echo U('downData');?>";
                var param = $('form.search').serialize();
                down_url = down_url + '?' + param;
                location.href = down_url;
            });

            $('body').on('click', '.search-li li', function () {
                $('input[name=order_status]').val($(this).data('id'));
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            });


            $('body').on('click', '.set-order', function () {
                $('#set-order #order-id').val($(this).data('id'));
                $('#set-order #label').text($(this).data('label'));
                $('#set-order #order-field-value').val($(this).data('val'));
                $('#set-order #order-field').val($(this).data('field'));
                var title = $(this).text();
                index = layer.open({
                    type: 1,
                    title: title,
                    area: ['50%', '200px'],
                    content: $('#set-order')
                })
            })

            $('body').on('click', '.set-logistics', function () {
                $('#set-logistics input[name=order_id]').val($(this).data('id'));
                $('#set-logistics input[name=logistics_english_name]').val('');
                $('#set-logistics input[name=logistics_code]').val('');
                index = layer.open({
                    type: 1,
                    title: '重新发货',
                    area: ['50%', '300px'],
                    content: $('#set-logistics')
                })
            })

            /**
             * 设置操作备注
             */
            form.on('submit(set-logistics)', function (data) {
                var _this = this;
                _this.disabled = true;
                $.post("<?php echo U('setLogistics');?>", data.field, function (res) {
                    _this.disabled = false;
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            layer.closeAll();
                            tableObj.reload()
                        });
                    } else {
                        layer.msg(res.info);
                    }
                });
                return false;
            });

            $('body').on('click', '.order-confirm', function () {
                var order_id = $(this).data('id');
                layer.confirm('您确定该订单无误，确定后将进入备货中？', {title: "订单确认"}, function () {
                    $.post("<?php echo U('Order/orderConfirm');?>", {order_id: order_id}, function (res) {
                        if (res.status == 1) {
                            layer.closeAll();
                            layer.msg(res.info, function () {
                                tableObj.reload();
                            });
                        } else {
                            layer.msg(res.info);
                        }
                    });
                });
            });
            /**
             * 设置操作备注
             */
            form.on('submit(set-order)', function (data) {
                var _this = this;
                _this.disabled = true;
                $.post("<?php echo U('setOrderField');?>", data.field, function (res) {
                    _this.disabled = false;
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            layer.closeAll();
                            tableObj.reload()
                        });
                    } else {
                        layer.msg(res.info);
                    }
                });
                return false;
            });


            function getData(url) {
                tableObj = table.render({
                    elem: '#table'
                    , url: url
                    , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
                        layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                        , groups: 5 //只显示 1 个连续页码
                    }
                    , height: 'full-340'
                    , cellMinWidth: 100
                    , cols: [[
                        {field: 'order_id', width: '180', title: '订单编号', sort: true}
                        , {field: 'shop_name', title: '店铺名称', width: 120}
                        , {field: 'img', title: '图片', width: 150, templet: '#img'}
                        , {field: 'product_name', title: '商品信息', width: 200, templet: '#product', sort: true}
                        , {field: 'combo_num', title: '数量', width: 60}
                        , {field: 'total_amount', title: '付款金额'}
                        , {field: 'post_receiver', title: '收件人'}
                        , {field: 'post_tel', title: '收件电话', width: 120}
                        , {field: 'post_address', title: '收件地址'}
                        , {field: 'order_status_view', title: '订单状态'}
                        , {field: 'create_time', title: '创建时间', width: 180, sort: true}
                        , {field: 'pay_type_name', title: '支付方式'}
                        , {field: 'product_pay_amount', title: '拍单金额'}
                        , {field: 'commission', title: '达人佣金'}
                        , {field: 'type_name', title: '订单来源', width: 150}
                        , {field: 'buyer_words', title: '用户留言'}
                        , {field: 'logistics_name', title: '快递信息', width: 200}
                        , {field: 'logistics_code', title: '快递单号', width: 300}
                        , {field: 'ali_order_id', title: '阿里订单号', width: 300}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit', width: 200}
                    ]]
                });
            }
        })
    </script>

</body>
</html>