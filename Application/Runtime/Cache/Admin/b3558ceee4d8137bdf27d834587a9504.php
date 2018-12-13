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
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 'img'): ?><div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                        <figure>
                                            <div class="img-wrap">
                                                <img src="<?php echo ($vo["img"]); ?>">
                                            </div>
                                            <div class="bottom-bar">
                                                <figcaption>
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong>
                                                    <?php echo ($vo["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
                                                </figcaption>
                                            </div>
                                        </figure>
                                    </div>
                                    <?php else: ?>
                                    <div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                        <figure>
                                            <div class="img-wrap">
                                                <img src="<?php echo ($vo["img"]); ?>">
                                                <div class="pswp__price_tag position_left"
                                                     style="top:20%;left:60%;">
                                                    <span>¥<?php echo ($vo["price"]); ?></span>
                                                    <i class="dot-con">
                                                        <i class="dot-animate"></i>
                                                        <i class="dot"></i>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="bottom-bar">
                                                <h2>
                                                    <?php echo ($vo["name"]); ?>
                                                    <a class="direct-link"
                                                       href="<?php echo ($vo["real_url"]); ?>"
                                                       target="_blank">直达链接</a>
                                                </h2>
                                                <figcaption>
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["description"]); ?>
                                                </figcaption>
                                            </div>
                                        </figure>
                                    </div>
                                    <div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                        <figure>
                                            <?php $temp_son = $vo['attached_imgs']; ?>
                                            <div class="img-wrap">
                                                <img src="<?php echo ($temp_son[0]["img"]); ?>">
                                            </div>
                                            <div class="bottom-bar">
                                                <h2>
                                                    <?php echo ($vo["name"]); ?>
                                                    <a class="direct-link"
                                                       href="<?php echo ($vo["real_url"]); ?>"
                                                       target="_blank">直达链接</a>
                                                </h2>
                                                <figcaption>
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($temp_son[0]["description"]); ?>
                                                </figcaption>
                                            </div>
                                        </figure>
                                    </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>

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
                <div class="layui-field-box" style="min-width: 590px;">
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
                            <label class="layui-form-label">所属领域</label>
                            <div class="layui-input-inline">
                                <select name="classify" id="classify" class="form-control">
                                    <option value="">请选择</option>
                                    <?php if(is_array($classify_data)): foreach($classify_data as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文章类型</label>
                            <div class="layui-input-block">
                                <input type="radio" name="news_type" value="1" title="图集" checked>
                                <input type="radio" name="news_type" value="2" title="专辑">
                            </div>
                        </div>
                        <!--eq name="user_id" value="5">
                            <div class="layui-form-item">
                                <label class="layui-form-label">文章类型</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="news_type" value="1" title="图集" checked>
                                    <input type="radio" name="news_type" value="2" title="专辑">
                                </div>
                            </div>
                        </eq>
                        <eq name="group_id" value="1">
                            <div class="layui-form-item">
                                <label class="layui-form-label">文章类型</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="news_type" value="1" title="图集" checked>
                                    <input type="radio" name="news_type" value="2" title="专辑">
                                </div>
                            </div>
                        </eq-->
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-inline">
                                <button class="layui-btn" lay-submit lay-filter="save" type="submit">保存</button>
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


    <script src="/Public/Admin/js/swipe.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script>
        $(function () {
            laydate.render({
                elem: '#send-time'
                , type: 'datetime'
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
                layer.open({
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

            form.on('submit(save)', function (data) {
                var _this = $(this);
                if (data.field.account_id == 0) {
                    layer.msg('请选择文章发布账号！');
                    return false;
                }
                if (!data.field.title) {
                    layer.msg('请输入文章标题！');
                    return false;
                }
                if (data.field.classify == 0) {
                    layer.msg('请选择文章所属领域！');
                    return false;
                }
                _this.addClass('layui-disabled');
                var url = "<?php echo U('Sale/saveNews');?>";
                _this.text('正在保存中...');
                $.post(url, data.field, function (res) {
                    layer.msg(res.info);
                    if (res.status == 1) {
                        setTimeout(function () {
                            window.parent.location.href = "<?php echo U('Sale/newsList');?>";
                        }, 3000)
                    } else {
                        _this.text('保存');
                        _this.removeClass('layui-disabled');
                    }
                })
                return false
            });
        });
    </script>

</body>
</html>