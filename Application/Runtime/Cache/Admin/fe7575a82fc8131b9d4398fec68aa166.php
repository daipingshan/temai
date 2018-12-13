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
    
    <style type="text/css">
        .item_content ul {
            list-style: none;
        }

        .item_content ul li {
            width: 300px;
            height: 200px;
            float: left;
            margin: 10px
        }

        .item_content {
            float: left;
        }

        .item {
            width: 300px;
            height: 200px;
            line-height: 200px;
            text-align: center;
            cursor: pointer;
            background: #ccc;
            overflow: hidden;
        }

        .item img {
            width: 300px;
            border-radius: 6px;
        }

        .item .text {
            position: absolute;
            left: 0;
            bottom: 0;
            background: rgba(0, 0, 0, .6);
            font-size: 12px;
            width: 100%;
            text-align: left;
            color: #fff;
            line-height: 20px;
            padding: 5px;
        }

        button.save-item {
            margin: 50px auto;
        }

        .close {
            display: block;
            width: 50px;
            height: 50px;
            top: -90px;
            right: 20px;
            z-index: 9999;
            position: absolute;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            color: aliceblue;
            margin: 10px auto
        }


    </style>

    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px">
    
    <div class="item_container">
        <div class="item_content" id="imageChange">
            <ul>
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <div class="item" data-id="<?php echo ($vo["id"]); ?>">
                            <img src="<?php echo ($vo["img"]); ?>">
                            <div class="text">
                                <?php echo ($vo["describe_info"]); ?>
                            </div>
                            <span data-id="<?php echo ($vo["id"]); ?>" class="rmPicture close"><i class="layui-icon layui-icon-delete"
                                                                                style="font-size: 30px;color: red;"></i></span>
                        </div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <div style="width: 100px;margin: 0 auto">
        <button type="button" class="layui-btn layui-btn-danger save-item">保存</button>
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


    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
    <script>
        /*
           作者: 谢泽龙
           联系QQ: 454675335 (灬丿Spam丶)
           时间: 2014-9-24
           www.jq22.com
       */

        $(function () {
            function Pointer(x, y) {
                this.x = x;
                this.y = y;
            }

            function Position(left, top) {
                this.left = left;
                this.top = top;
            }

            $(".item_container .item").each(function (i) {
                this.init = function () { // 初始化
                    this.box = $(this).parent();
                    $(this).attr("index", i).css({
                        position: "absolute",
                        left: this.box.offset().left,
                        top: this.box.offset().top
                    }).appendTo(".item_container");
                    this.drag();
                },
                    this.move = function (callback) {  // 移动
                        $(this).stop(true).animate({
                            left: this.box.offset().left,
                            top: this.box.offset().top
                        }, 500, function () {
                            if (callback) {
                                callback.call(this);
                            }
                        });
                    },
                    this.collisionCheck = function () {
                        var currentItem = this;
                        var direction = null;
                        $(this).siblings(".item").each(function () {
                            if (
                                currentItem.pointer.x > this.box.offset().left &&
                                currentItem.pointer.y > this.box.offset().top &&
                                (currentItem.pointer.x < this.box.offset().left + this.box.width()) &&
                                (currentItem.pointer.y < this.box.offset().top + this.box.height())
                            ) {
                                // 返回对象和方向
                                if (currentItem.box.offset().top < this.box.offset().top) {
                                    direction = "down";
                                } else if (currentItem.box.offset().top > this.box.offset().top) {
                                    direction = "up";
                                } else {
                                    direction = "normal";
                                }
                                this.swap(currentItem, direction);
                            }
                        });
                    },
                    this.swap = function (currentItem, direction) { // 交换位置
                        if (this.moveing) return false;
                        var directions = {
                            normal: function () {
                                var saveBox = this.box;
                                this.box = currentItem.box;
                                currentItem.box = saveBox;
                                this.move();
                                $(this).attr("index", this.box.index());
                                $(currentItem).attr("index", currentItem.box.index());
                            },
                            down: function () {
                                // 移到上方
                                var box = this.box;
                                var node = this;
                                var startIndex = currentItem.box.index();
                                var endIndex = node.box.index();
                                ;
                                for (var i = endIndex; i > startIndex; i--) {
                                    var prevNode = $(".item_container .item[index=" + (i - 1) + "]")[0];
                                    node.box = prevNode.box;
                                    $(node).attr("index", node.box.index());
                                    node.move();
                                    node = prevNode;
                                }
                                currentItem.box = box;
                                $(currentItem).attr("index", box.index());
                            },
                            up: function () {
                                // 移到上方
                                var box = this.box;
                                var node = this;
                                var startIndex = node.box.index();
                                var endIndex = currentItem.box.index();
                                ;
                                for (var i = startIndex; i < endIndex; i++) {
                                    var nextNode = $(".item_container .item[index=" + (i + 1) + "]")[0];
                                    node.box = nextNode.box;
                                    $(node).attr("index", node.box.index());
                                    node.move();
                                    node = nextNode;
                                }
                                currentItem.box = box;
                                $(currentItem).attr("index", box.index());
                            }
                        }
                        directions[direction].call(this);
                    },
                    this.drag = function () { // 拖拽
                        var oldPosition = new Position();
                        var oldPointer = new Pointer();
                        var isDrag = false;
                        var currentItem = null;
                        $(this).mousedown(function (e) {
                            e.preventDefault();
                            oldPosition.left = $(this).position().left;
                            oldPosition.top = $(this).position().top;
                            oldPointer.x = e.clientX;
                            oldPointer.y = e.clientY;
                            isDrag = true;

                            currentItem = this;

                        });
                        $(document).mousemove(function (e) {
                            var currentPointer = new Pointer(e.clientX, e.clientY);
                            if (!isDrag) return false;
                            $(currentItem).css({
                                "opacity": "0.8",
                                "z-index": 999
                            });
                            var left = currentPointer.x - oldPointer.x + oldPosition.left;
                            var top = currentPointer.y - oldPointer.y + oldPosition.top;
                            $(currentItem).css({
                                left: left,
                                top: top
                            });
                            currentItem.pointer = currentPointer;
                            // 开始交换位置

                            currentItem.collisionCheck();


                        });
                        $(document).mouseup(function () {
                            if (!isDrag) return false;
                            isDrag = false;
                            currentItem.move(function () {
                                $(this).css({
                                    "opacity": "1",
                                    "z-index": 0
                                });
                            });
                        });
                    }
                this.init();
            });
        });

        $('.save-item').click(function () {
            var item_data = []
            $('.item').each(function () {
                var shop_goods_id = $(this).data('id');
                var sort = parseInt($(this).attr('index'));
                item_data[sort] = shop_goods_id;
            });
            $.post("<?php echo U('setItemSort');?>", {item_data: item_data}, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.parent.layer.closeAll();
                    })
                } else {
                    layer.msg(res.info);
                }
            })
        })
        $('.item .close').click(function () {
            var shop_goods_id = $(this).data('id');
            layer.confirm("你确定要删除该选品吗？", function () {
                $.post("<?php echo U('TopLine/delItemCache');?>", {id: shop_goods_id}, function (res) {
                    layer.msg(res.info);
                    if (res.status == 1) {
                        parent.$('#cart .cart-num').text(parent.$('#cart .cart-num').text() - 1);
                        location.reload();
                    }
                })
            })
        })
    </script>

</body>
</html>