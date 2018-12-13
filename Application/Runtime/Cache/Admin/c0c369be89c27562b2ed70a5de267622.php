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
    
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/previewgallery.css?v=<?php echo C('CSS_VER');?>">
    <style type="text/css">
        .layui-layer-page {
            background-color: #fff !important;
            background-size: cover;
            color: #333 !important;
        }
    </style>

    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px">
    
    <div class="layui-row">
        <div class="layui-col-xs12 layui-col-md5">
            <div id="simulator">
                <div id="main">
                    <div id="gallery" class="swipe" style="visibility: visible;">
                        <div class="swipe-wrap">
                            <!--figcaption为图片描述，控制在200字以内-->
                            <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide" data-id="<?php echo ($vo["id"]); ?>" data-sort="<?php echo ($key+1); ?>">
                                    <figure>
                                        <div class="img-wrap">
                                            <img src="<?php echo ($vo["img"]); ?>">
                                            <div class="pswp__price_tag position_left" style="top:20%;left:60%;">
                                                <i class="dot-con">
                                                    <i class="dot-animate"></i>
                                                    <i class="dot"></i>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="bottom-bar">
                                            <h2>
                                                <?php echo ($vo["title"]); ?>
                                                <a class="direct-link"
                                                   href="<?php echo ($vo["url"]); ?>"
                                                   target="_blank">直达链接</a>
                                            </h2>
                                            <figcaption>
                                                <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["name"]); ?>
                                            </figcaption>
                                        </div>
                                    </figure>
                                </div><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>
                    <div class="btn-container">
                        <span class="prevBtn" onclick="mySwipe.prev();"></span>
                        <span class="nextBtn" onclick="mySwipe.next();"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-md7" style="margin-top: 50px">
            <fieldset class="layui-elem-field">
                <legend>保存文章</legend>
                <div class="layui-field-box">
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
                            <label class="layui-form-label">文章封面</label>
                            <div class="layui-input-block">
                                <input type="radio" lay-filter="img-type" name="img_type" value="3" title="三图" checked>
                                <input type="radio" lay-filter="img-type" name="img_type" value="1" title="单图">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-block">
                                <div class="layui-row">
                                    <div class="layui-col-xs4 img-covers select-img">
                                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                                    </div>
                                    <div class="layui-col-xs4 img-covers select-img more-img">
                                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                                    </div>
                                    <div class="layui-col-xs4 img-covers select-img more-img">
                                        <img src="/Public/Admin/images/add.jpg" class="add-logo"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">定时发表</label>
                            <div class="layui-input-inline">
                                <input type="text" name="send_time" id="send-time" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-inline">
                                <button class="layui-btn" lay-submit lay-filter="save" type="button">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
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

    <div id="select-top-img" style="display: none;padding: 20px">
        <div class="layui-row">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="layui-col-xs4 img-covers modal-img">
                    <img src="<?php echo ($row["img"]); ?>" data-id="<?php echo ($row["shop_goods_id"]); ?>" style="width: 100%">
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>


    <script src="/Public/Admin/js/swipe.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script>
        $(function () {
            var img_index = 0;
            var img_id = [];
            var layer_index = 0;
            laydate.render({
                elem: '#send-time'
                , type: 'datetime'
            });
            form.on('radio(img-type)', function (data) {
                if (data.value == 1) {
                    $('.more-img').hide();
                } else {
                    $('.more-img').show();
                }
            });
            form.render();
            if ($('#gallery').find('.swipe-wrap').children().length) {
                window.mySwipe = $('#gallery').Swipe({
                    startSlide: 0,
                    continuous: false,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function (index, elem) {
                    },
                    transitionEnd: function (index, elem) {
                    }
                }).data('Swipe');
            }
            $('.select-img').click(function () {
                img_index = $(this).index();
                layer_index = layer.open({
                    type: 1,
                    area: ['50%', '50%'],
                    fix: false, //不固定
                    maxmin: true,
                    shadeClose: true,
                    shade: 0.4,
                    title: '选择封面图',
                    content: $('#select-top-img'),
                    cancel: function () {
                        $('#select-top-img').hide();
                    }
                });
            });

            $('.modal-img').click(function () {
                var id = $(this).find('img').data('id');
                var img = $(this).find('img').attr('src');
                img_id[img_index] = id;
                $('.select-img:eq(' + img_index + ')').html("<img src='" + img + "' style='width: 100%'>");
                $('#select-top-img').hide();
                layer.close(layer_index);
            });
            form.on('submit(save)', function (data) {
                var _this = $(this);
                var img_type = data.field.img_type;
                var img = [];
                if (data.field.account_id == 0) {
                    layer.msg('请选择文章发布账号！');
                    return false;
                }
                if (!data.field.title) {
                    layer.msg('请输入文章标题！');
                    return false;
                }
                var img_len = img_id.length;
                if (img_len == 0) {
                    layer.msg('请选择封面图！');
                    return false;
                }
                if (img_type == 3) {
                    if (img_len != 3) {
                        var n = 3 - img_len;
                        layer.msg('您的封面图已选择' + img_len + '张，还差' + n + '张');
                        return false;
                    }
                    img = img_id;
                } else {
                    img[0] = img_id[0];
                }
                data.field.img = img;
                _this.addClass('layui-disabled');
                var url = "<?php echo U('TopLine/saveShopNews');?>";
                _this.text('正在保存中...');
                $.post(url, data.field, function (res) {
                    layer.msg(res.info);
                    if (res.status == 1) {
                        setTimeout(function () {
                            window.parent.location.href = "<?php echo U('TopLine/shopNewsList');?>";
                        }, 3000)
                    } else {
                        _this.text('保存');
                        _this.removeClass('layui-disabled');
                    }
                })

            });
        });
    </script>

</body>
</html>