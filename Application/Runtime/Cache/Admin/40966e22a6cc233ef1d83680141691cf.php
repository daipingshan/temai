<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>特卖管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/font.css?v=<?php echo C('CSS_VER');?>">
    <link rel="stylesheet" href="/Public/Admin/css/layer_open.css?v=<?php echo C('CSS_VER');?>">
    
    <link rel="stylesheet" href="/Public/Admin/css/item_box.css">
    <style type="text/css">
        .layui-container {
            margin: 0 !important;
            padding: 0 !important;
            width: 100%;
        }

        .layui-layer-page {
            background-color: #fff !important;
            background-size: cover;
            color: #333 !important;
        }

        .default-bgcolor {
            background: rgba(255, 255, 255, 0.4);
        }

        .layui-box {
            margin: 0 20px 20px 20px;
            padding-top: 150px;
        }

        .layui-form {
            position: fixed;
            z-index: 10;
            background: #fff;
            width: 100%;
            padding: 20px 50px;
            box-shadow: 2px 2px 12px 0 rgba(0, 0, 0, .6);
            height: 106px;
        }

        .item-box:hover {
            box-shadow: 2px 2px 12px 0 rgba(0, 0, 0, .6);
        }
    </style>

    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px">
    
    <form class="layui-form" action="<?php echo U('Sale/selectItems');?>" name="search">
        <div class="layui-form-pane">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <select name="category1_id" lay-filter="cate">
                        <option value="0">请选择一级分类</option>
                        <?php if(is_array($cate)): foreach($cate as $k=>$cate_row): ?><option value="<?php echo ($k); ?>"
                            <?php if($param['category1_id'] == $k): ?>selected<?php endif; ?>
                            ><?php echo ($cate_row); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="category2_id" id="cate">
                        <option value="0">请选择二级分类</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="keyword_type">
                        <option value="word">商品名称</option>
                        <option value="platform_sku_id"
                        <?php if(($param["keyword_type"]) == "platform_sku_id"): ?>selected<?php endif; ?>
                        >商品ID</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="keyword_value" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['keyword_value']); ?>">
                </div>
                <div class="layui-input-inline">
                    <select name="keyword_type_shop">
                        <option value="word">店铺名称</option>
                        <option value="shop_id"
                        <?php if(($param["keyword_type_shop"]) == "shop_id"): ?>selected<?php endif; ?>
                        >店铺ID</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="keyword_value_shop" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['keyword_value_shop']); ?>">

                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-inline" style="width: 400px">
                    <button type="button" class="layui-btn btn-sort btn-null" value="">
                        综合排序
                    </button>
                    <button type="button" class="layui-btn layui-btn-primary  btn-sort btn-hotrank" data-type="desc"
                            value="hotrank">
                        热度<b>↓</b>
                    </button>
                    <button type="button" class="layui-btn layui-btn-primary btn-sort btn-cos_ratio" data-type="desc"
                            value="cos_ratio">
                        佣金比例<b>↓</b>
                    </button>
                    <button type="button" class="layui-btn layui-btn-primary btn-sort btn-sku_price" data-type="desc"
                            value="sku_price">
                        价格<b>↓</b>
                    </button>
                </div>
                <label class="layui-form-label">佣金比例</label>
                <div class="layui-input-inline">
                    <input type="text" name="cos_ratio_down" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['cos_ratio_down']); ?>"
                           style="width: 40%;display: inline-block">&nbsp;&nbsp;-
                    <input type="text" name="cos_ratio_up" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['cos_ratio_up']); ?>"
                           style="width: 40%;display: inline-block">
                </div>
                <label class="layui-form-label">价格</label>
                <div class="layui-input-inline">
                    <input type="text" name="sku_price_down" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['sku_price_down']); ?>"
                           style="width: 40%;display: inline-block">&nbsp;&nbsp;-
                    <input type="text" name="sku_price_up" placeholder="请输入" autocomplete="off"
                           class="layui-input" value="<?php echo ($param['sku_price_up']); ?>"
                           style="width: 40%;display: inline-block">
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <input type="hidden" name="order" id="order" value="<?php echo ($param['order']); ?>"/>
                    <input type="hidden" name="sort" id="sort" value="<?php echo ($param['sort']); ?>"/>
                    <input type="hidden" name="type" value="<?php echo ($type); ?>"/>
                    <button class="layui-btn"><i class="layui-icon">
                        &#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <div class="layui-box">
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="layui-row">
                <?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
                        <div class="item-box" style="border: 1px solid rgba(0, 0, 0, 0.3);">
                            <div class="img">
                                <img lay-src="<?php echo ($row["figure"]); ?>">
                                <a href="<?php echo ($vo["sku_url"]); ?>" target="_blank">
                                    <div class="intro">
                                        <p style="color: #fff;font-size: 14px;line-height: 20px"><?php echo ($row["sku_title"]); ?></p>
                                    </div>
                                </a>
                            </div>
                            <div class="item-content">
                                <a href="<?php echo ($row["sku_url"]); ?>" target="_blank"><p class="item-title" style="color: #333">
                                    <?php echo ($row['shop_name']); ?></p>
                                </a>
                                <div class="layui-row float-box">
                                    <div class="layui-col-xs5">
                                        <div class="price" style="text-align: center">
                                            <b style="color: red">￥<?php echo ($row["sku_price"]); ?></b>
                                        </div>
                                    </div>
                                    <div class="layui-col-xs7">
                                        <div class="time" style="color: #666;text-align: center">
                                            热度： <span style="color: red"><?php echo ($row['hotrank']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-row float-box">
                                    <div class="layui-col-xs5">
                                        <div class="time" style="color: #666;text-align: center">
                                            佣金比率：<span style="color: red"><?php echo ($row['cos_info']['cos_ratio']*100); ?>%</span>
                                        </div>
                                    </div>
                                    <div class="layui-col-xs7">
                                        <div class="time" style="color: #666;text-align: center">
                                            预估：<span style="color: red">￥<?php echo ($row['cos_info']['cos_fee']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-row float-box">
                                    <button class="layui-btn select-item" data-id="<?php echo ($row["platform_sku_id"]); ?>"
                                            type="button">
                                        选择商品
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php echo ($page); ?>
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
</script>
<!-- 中部结束 -->


    <script>
        var sort = "<?php echo ($param['sort']); ?>";
        var order = "<?php echo ($param['order']); ?>";
        var cate_id = "<?php echo ($param['category1_id']); ?>";
        var cate_sub_id = "<?php echo ($param['category2_id']); ?>";
        if (cate_id > 0) {
            setSubCate(cate_id, cate_sub_id);
        }
        form.render();
        flow.lazyimg();
        form.on('select(cate)', function (data) {
            if (data.value > 0) {
                setSubCate(data.value);
            }
        });
        function setSubCate(cate_id, sub_cate_id) {
            $.get("<?php echo U('Sale/ajaxSubCate');?>", {cate_id: cate_id}, function (res) {
                var html = "<option value='0'>请选择二级分类</option>";
                var cate_data = res.info.data;
                $.each(cate_data, function (i, e) {
                    if (parseInt(sub_cate_id) > 0 && sub_cate_id == i) {
                        html += "<option value='" + i + "' selected>" + e + "</option>";
                    } else {
                        html += "<option value='" + i + "'>" + e + "</option>";
                    }
                });
                $('#cate').html(html);
                form.render('select');
            });
        }

        if (sort) {
            $('.btn-null').addClass('layui-btn-primary');
            $('.btn-' + sort).removeClass('layui-btn-primary');
            if (order == 'asc') {
                $('.btn-' + sort).attr('data-type', 'desc');
                $('.btn-' + sort + ' b').text('↑');
            } else {
                $('.btn-' + sort).attr('data-type', 'asc');
                $('.btn-' + sort + ' b').text('↓');
            }
        }

        $('.btn-sort').click(function () {
            $('#order').val($(this).data('type'));
            $('#sort').val($(this).val());
            $('form').submit();
        });

        $('button.select-item').click(function () {
            var type = "<?php echo ($type); ?>";
            var goods_id = $(this).data('id');
            var param = $('form').serialize();
            $.get("<?php echo U('saveParam');?>", param, function () {
                if (type == 'sale') {
                    window.parent.location.href = "<?php echo U('Sale/itemsList');?>" + '?shop_goods_id=' + goods_id;
                } else {
                    window.parent.location.href = "<?php echo U('TopLine/itemsList');?>" + '?shop_goods_id=' + goods_id;
                }

            });
        });
    </script>

</body>
</html>