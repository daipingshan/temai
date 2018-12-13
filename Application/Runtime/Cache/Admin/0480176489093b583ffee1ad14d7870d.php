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
        <div class="layui-col-xs7">
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
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
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
                                                    <a class="direct-link"
                                                       href="<?php echo ($vo["real_url"]); ?>"
                                                       target="_blank">直达链接</a>
                                                </h2>
                                                <figcaption>
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($temp_son[0]["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
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
        <div class="layui-col-xs5">
            <fieldset class="layui-elem-field">
                <legend>商品分类信息</legend>
                <div class="layui-field-box">
                    <?php if(is_array($cate_data)): $i = 0; $__LIST__ = $cate_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i; if($cate['name'] == '图集'): ?><h3 style="text-align: center;color: #009688"><?php echo ($cate['name']); ?>-【<?php echo ($cate['num']); ?>】</h3>
                            <?php else: ?>
                            <h3 style="text-align: center;color: #FF5722"><?php echo ($cate['name']); ?>-【<?php echo ($cate['num']); ?>】</h3><?php endif; ?>

                        <hr class="layui-bg-black"><?php endforeach; endif; else: echo "" ;endif; ?>
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
        });
    </script>

</body>
</html>