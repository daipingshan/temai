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
                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide" data-id="<?php echo ($vo["id"]); ?>" data-sort="<?php echo ($key+1); ?>">
                            <figure>
                                <?php $temp = json_encode($vo); ?>
                                <a class="update-item" href="javascript:;" onclick="updateItemCache($(this))"
                                   data-val='<?php echo ($temp); ?>'>编辑商品</a>
                                <a class="del-item" href="javascript:;" data-id="<?php echo ($vo["id"]); ?>"
                                   data-url="<?php echo U('TopLine/delItemCache');?>"
                                   onclick="delItemCache($(this))">一键删除</a>
                                <div class="img-wrap">
                                    <img src="<?php echo ($vo["img"]); ?>">
                                    <div class="pswp__price_tag position_left" style="top:20%;left:60%;">
                                        <span>¥<?php echo ($vo["price"]); ?></span>
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
                                           href="<?php echo ($vo["tmall_url"]); ?>"
                                           target="_blank">直达链接</a>
                                    </h2>
                                    <figcaption>
                                        <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["describe_info"]); ?>
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

    <div id="update-item" style="display: none;padding: 20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">商品封面</label>
                <div style="float: left;width: 20%">
                    <img id="covers-img" style="width: 90%"/>
                </div>
                <div class="layui-upload-drag" id="upload" style="float: right;">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品文案</label>
                <div class="layui-input-block" style="position: relative;">
                    <textarea placeholder="请输入内容" id="update_info" class="layui-textarea"></textarea>
                    <div style="position: absolute;bottom: 0;right: 0">
                        <button class="layui-btn layui-btn-sm translate" data-url="<?php echo U('TopLine/translate');?>"
                                type="button">
                            伪原创
                        </button>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">商品排序</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" id="sort" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <input type="hidden" name="update_id" id="update-id"/>
                    <input type="hidden" name="update_img" id="update-img"/>
                    <input type="hidden" name="update_img_key" id="update-img-key"/>
                    <button class="layui-btn" id="save-item" type="button">编辑</button>
                </div>
            </div>
        </form>
    </div>


    <script src="/Public/Admin/js/swipe.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script>
        $(function () {
            var upload_url = "<?php echo U('TopLine/uploadTopImg');?>";
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
            form.render();
            upload.render({
                elem: '#upload'
                , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
                , url: upload_url
                , accept: 'file' //普通文件
                , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
                , done: function (res) {
                    if (res.status == 1) {
                        $('#covers-img').attr('src', res.info.url);
                        $('#update-img').val(res.info.url);
                        $('#update-img-key').val(res.info.img_key);
                        layer.msg('上传成功！');
                    } else {
                        layer.msg(res.info);
                    }
                }
            });
            $('.translate').click(function () {
                var _this = $(this);
                var content = _this.parents('.layui-input-block').find('textarea').val();
                if (!content) {
                    layer.msg('文案内容不能为空！');
                    return false;
                }
                $.post(_this.data('url'), {content: content}, function (res) {
                    layer.msg(res.info.msg)
                    if (res.status == 1) {
                        _this.parents('.layui-input-block').find('textarea').val(res.info.content);
                    }
                });
            });
            //监听提交
            $('#save-item').on('click', function () {
                var _this = $(this);
                var id = $('#update-id').val();
                var content = $('#update_info').val();
                var img_url = $('#update-img').val();
                var img_key = $('#update-img-key').val();
                var sort = $('#sort').val();
                if (!img_url || !img_key) {
                    layer.msg('图片信息有误，请上传后修改！');
                    return false;
                }
                if (!content) {
                    layer.msg(' 请输入商品文案！');
                    return false;
                }
                _this.addClass('layui-disabled');
                $.post("<?php echo U('TopLine/updateItemCache');?>", {
                    id: id,
                    content: content,
                    img_url: img_url,
                    img_key: img_key,
                    sort: sort
                }, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.msg(res.info);
                        _this.removeClass('layui-disabled');
                    }
                })
            })
        });
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

        function updateItemCache(obj) {
            var item = obj.data('val');
            $('#covers-img').attr('src', item.img);
            $('#update_info').val(item.describe_info);
            $('#update-id').val(item.id);
            $('#update-img').val(item.img);
            $('#update-img-key').val(item.json_data.uri);
            $('#sort').val(item.sort);
            layer.open({
                type: 1,
                area: ['80%', '80%'],
                fix: false, //不固定
                maxmin: true,
                shadeClose: true,
                shade: 0.4,
                title: '编辑商品',
                content: $('#update-item'),
                cancel: function () {
                    $('#update-item').hide();
                }
            });
        }
    </script>

</body>
</html>