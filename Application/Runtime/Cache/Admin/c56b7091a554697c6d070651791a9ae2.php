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
        .layui-input-inline {
            width: 60% !important;
        }
    </style>

    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px">
    
    <!-- 右侧内容框架，更改从这里开始 -->
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">
                账号名称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="username"
                <?php if(!empty($info)): ?>value="<?php echo ($info['username']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入账号名称">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">头条ID</label>
            <div class="layui-input-block">
                <input type="text" name="media_id"
                <?php if(!empty($info)): ?>value="<?php echo ($info['media_id']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入头条ID">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">账号cookie</label>
            <div class="layui-input-block">
                <textarea name="cookie" placeholder="请输入账号cookie" class="layui-textarea" rows="10"><?php if(!empty($info)): echo ($info['cookie']); endif; ?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <?php if(!empty($info)): ?><input type="hidden" name="id" value="<?php echo ($info['id']); ?>"><?php endif; ?>
            <button class="layui-btn" lay-filter="save" lay-submit="save" type="submit">
                <?php if(!empty($info)): ?>编辑
                    <?php else: ?>
                    添加<?php endif; ?>
            </button>
        </div>
    </form>
    <!-- 右侧内容框架，更改从这里结束 -->

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


    <script type="text/javascript">
        form.render();
        //监听提交
        form.on('submit(save)', function (data) {
            var _this = $(this);
            _this.addClass('layui-disabled');
            var url = "<?php echo U('TopLine/saveAccount');?>";
            $.post(url, data.field, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.parent.location.reload();
                    });
                } else {
                    _this.removeClass('layui-disabled');
                    layer.msg(res.info);
                }
            });
            return false;
        });
    </script>

</body>
</html>