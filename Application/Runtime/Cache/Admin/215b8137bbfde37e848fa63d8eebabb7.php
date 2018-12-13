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
    
    <div id="simulator">
        <div id="main">
            <div id="gallery" class="swipe" style="visibility: visible;">
                <div class="swipe-wrap">
                    <!--figcaption为图片描述，控制在200字以内-->
                    <!--img标签width和height属性须事先给出，由于懒加载的需要，必须把"src"改写为"alt-src" -->
                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                            <figure>
                                <a class="del-item" href="javascript:;" data-id="<?php echo ($vo["shop_goods_id"]); ?>"
                                   data-url="<?php echo U('TopLine/delShopItemCache');?>"
                                   onclick="delItemCache($(this))">一键删除</a>
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
                                        <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["title"]); ?>
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
        })
        /**
         * @param obj
         */
        function delItemCache(obj) {
            $.post(obj.data('url'), {id: obj.data('id')}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    parent.$('#cart .cart-num').text(parent.$('#cart .cart-num').text() - 1);
                    obj.parents('.swiper-slide').remove();
                }
            })
        }
    </script>

</body>
</html>