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
                            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 'img'): ?><div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                        <figure>
                                            <?php $temp = json_encode($vo); ?>
                                            <a class="update-item" href="javascript:;" onclick="updateImgCache($(this))"
                                               data-val='<?php echo ($temp); ?>'>编辑图集</a>
                                            <a class="del-item" href="javascript:;" data-id="<?php echo ($vo['shop_goods_id']); ?>"
                                               onclick="delItemCache($(this))"
                                               data-url="<?php echo U('Sale/delItemCache');?>">一键删除</a>
                                            <div class="img-wrap">
                                                <img src="<?php echo ($vo["img"]); ?>">
                                            </div>
                                            <div class="bottom-bar">
                                                <figcaption>
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong> <?php echo ($vo["description"]); ?>
                                                </figcaption>
                                            </div>
                                        </figure>
                                    </div>
                                    <?php else: ?>
                                    <div class="swiper-slide num-<?php echo ($key); ?>" data-id="<?php echo ($vo["shop_goods_id"]); ?>">
                                        <figure>
                                            <?php $temp = json_encode($vo); ?>
                                            <?php $temp_son = $vo['attached_imgs']; ?>
                                            <a class="update-item" href="javascript:;"
                                               onclick="updateItemCache($(this))"
                                               data-val='<?php echo ($temp); ?>' data-son-img="<?php echo ($temp_son[0]['img']); ?>"
                                               data-son-info="<?php echo ($temp_son[0]['description']); ?>">编辑商品</a>
                                            <a class="del-item" href="javascript:;" data-id="<?php echo ($vo['shop_goods_id']); ?>"
                                               onclick="delItemCache($(this))"
                                               data-url="<?php echo U('Sale/delItemCache');?>">一键删除</a>
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
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong>
                                                    <?php echo ($vo["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
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
                                                    <strong><?php echo ($key+1); ?>/<?php echo count($data);?></strong>
                                                    <?php echo ($temp_son[0]["description"]); ?>--<?php echo ($vo["sec_cid"]); ?>
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

    <div id="update-item-box" style="display: none;padding: 20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">商品标题</label>
                <div class="layui-input-block">
                    <input type="text" name="name" id="update-name" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">主图封面</label>
                <div style="float: left;width: 20%">
                    <img id="covers-img" style="width: 90%"/>
                </div>
                <div class="layui-upload-drag" id="upload" style="float: right;padding: 10px">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">副图封面</label>
                <div style="float: left;width: 20%">
                    <img id="covers-img-vice" style="width: 90%"/>
                </div>
                <div class="layui-upload-drag" id="upload-vice" style="float: right;padding: 10px">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs6">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="position: relative">
                            主图文案
                            <div style="position: absolute;bottom: -50px;right: 10px">
                                <button class="layui-btn layui-btn-sm translate" data-url="<?php echo U('Sale/translate');?>"
                                        type="button">
                                    伪原创
                                </button>
                            </div>
                        </label>
                        <div class="layui-input-block" style="position: relative;">
                            <textarea placeholder="请输入内容" id="update-info" class="layui-textarea"></textarea>
                        </div>
                    </div>
                </div>
                <div class="layui-col-xs6">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="position: relative">
                            副图文案
                            <div style="position: absolute;bottom: -50px;right: 10px">
                                <button class="layui-btn layui-btn-sm translate" data-url="<?php echo U('Sale/translate');?>"
                                        type="button">
                                    伪原创
                                </button>
                            </div>
                        </label>
                        <div class="layui-input-block" style="position: relative;">
                            <textarea placeholder="请输入内容" id="update-info-vice" class="layui-textarea"></textarea>

                        </div>
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
                    <input type="hidden" name="update_img_vice" id="update-img-vice"/>
                    <button class="layui-btn" id="save-item" type="button">编辑</button>
                </div>
            </div>
        </form>
    </div>
    <div id="update-img-box" style="display: none;padding: 20px;">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">图集封面</label>
                <div style="float: left;width: 20%">
                    <img id="img" style="width: 90%"/>
                </div>
                <div class="layui-upload-drag" id="upload-img" style="float: right;padding: 10px">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="position: relative">
                    图集文案
                    <div style="position: absolute;bottom: -50px;right: 10px">
                        <button class="layui-btn layui-btn-sm translate" data-url="<?php echo U('Sale/translate');?>"
                                type="button">
                            伪原创
                        </button>
                    </div>
                </label>
                <div class="layui-input-block" style="position: relative;">
                    <textarea placeholder="请输入内容" id="img-info" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">商品排序</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" id="img-sort" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <input type="hidden" name="update_id" id="img-id"/>
                    <input type="hidden" name="update_img" id="img-update-img"/>
                    <button class="layui-btn" id="save-img" type="button">编辑</button>
                </div>
            </div>
        </form>
    </div>


    <script src="/Public/Admin/js/swipe.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script>
        var upload_url = "<?php echo U('Sale/uploadSaleImg');?>";
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
        //拖拽上传
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
                    layer.msg('上传成功！');
                } else {
                    layer.msg(res.info);
                }
            }
        });
        upload.render({
            elem: '#upload-vice'
            , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
            , url: upload_url
            , accept: 'file' //普通文件
            , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
            , done: function (res) {
                if (res.status == 1) {
                    $('#covers-img-vice').attr('src', res.info.url);
                    $('#update-img-vice').val(res.info.url);
                    layer.msg('上传成功！');
                } else {
                    layer.msg(res.info);
                }
            }
        });

        upload.render({
            elem: '#upload-img'
            , size: 5 * 1024 * 1024 //限制文件大小，单位 KB
            , url: upload_url
            , accept: 'file' //普通文件
            , exts: 'jpg|png|jpeg|gif' //只允许上传压缩文件
            , done: function (res) {
                if (res.status == 1) {
                    $('#img').attr('src', res.info.url);
                    $('#img-update-img').val(res.info.url);
                    layer.msg('上传成功！');
                } else {
                    layer.msg(res.info);
                }
            }
        });

        $('.translate').click(function () {
            var _this = $(this);
            var content = _this.parents('.layui-form-item').find('textarea').val();
            if (!content) {
                layer.msg('文案内容不能为空！');
                return false;
            }
            $.post(_this.data('url'), {content: content}, function (res) {
                layer.msg(res.info.msg)
                if (res.status == 1) {
                    _this.parents('.layui-form-item').find('textarea').val(res.info.content);
                }
            });
        });

        $('#save-item').click(function () {
            var _this = $(this);
            var id = $('#update-id').val();
            var name = $('#update-name').val();
            var img = $('#update-img').val();
            var son_img = $('#update-img-vice').val();
            var info = $('#update-info').val();
            var son_info = $('#update-info-vice').val();
            var sort = $('#sort').val();
            if (!name) {
                layer.msg('请输入商品标题！');
                return false;
            }
            if (!img) {
                layer.msg('主图信息有误，请上传后修改！');
                return false;
            }
            if (!son_img) {
                layer.msg('副图信息有误，请上传后修改！');
                return false;
            }
            if (!info) {
                layer.msg(' 请输入主图文案！');
                return false;
            }
            if (!son_info) {
                layer.msg(' 请输入副图文案！');
                return false;
            }
            _this.addClass('layui-disabled');
            $.post("<?php echo U('Sale/updateItemCache');?>", {
                id: id,
                name: name,
                img: img,
                son_img: son_img,
                info: info,
                son_info: son_info,
                sort: sort
            }, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(res.info);
                    _this.removeClass('layui-disabled');
                }
            })
        })

        $('#save-img').click(function () {
            var _this = $(this);
            var id = $('#img-id').val();
            var img = $('#img-update-img').val();
            var info = $('#img-info').val();
            var sort = $('#img-sort').val();
            if (!img) {
                layer.msg('图集信息有误，请上传后修改！');
                return false;
            }
            if (!info) {
                layer.msg(' 请输入图集文案！');
                return false;
            }
            _this.addClass('layui-disabled', true);
            $.post("<?php echo U('updateImgCache');?>", {
                id: id,
                img: img,
                info: info,
                sort: sort
            }, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(res.info);
                    _this.removeClass('layui-disabled');
                }
            })
        })


        function updateItemCache(obj) {
            var item = obj.data('val');
            var son_img = obj.data('son-img');
            var son_info = obj.data('son-info');
            $('#update-id').val(item.shop_goods_id);
            $('#update-name').val(item.name);
            $('#covers-img').attr('src', item.img);
            $('#covers-img-vice').attr('src', son_img);
            $('#update-img').val(item.img);
            $('#update-img-vice').val(son_img);
            $('#update-info').val(item.description);
            $('#update-info-vice').val(son_info);
            $('#sort').val(item.sort);
            layer.open({
                type: 1,
                area: ['90%', '98%'],
                fix: false, //不固定
                maxmin: true,
                shadeClose: true,
                shade: 0.4,
                title: '编辑商品',
                content: $('#update-item-box'),
                cancel: function () {
                    $('#update-item-box').hide();
                }
            });
        }

        function updateImgCache(obj) {
            var img = obj.data('val');
            $('#img').attr('src', img.img);
            $('#img-info').val(img.description);
            $('#img-id').val(img.shop_goods_id);
            $('#img-sort').val(img.sort);
            $('#img-update-img').val(img.img);
            layer.open({
                type: 1,
                area: ['80%', '70%'],
                fix: false, //不固定
                maxmin: true,
                shadeClose: true,
                shade: 0.4,
                title: '编辑商品',
                content: $('#update-img-box'),
                cancel: function () {
                    $('#update-img-box').hide();
                }
            });
        }

        /**
         * @param obj
         */
        function delItemCache(obj) {
            $.post(obj.data('url'), {id: obj.data('id')}, function (res) {
                layer.msg(res.info);
                if (res.status == 1) {
                    parent.$('#cart .cart-num').text(parent.$('#cart .cart-num').text() - 1);
                    location.reload();
                }
            })
        }

    </script>

</body>
</html>