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
    
    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px">
    
    <!-- 右侧内容框架，更改从这里开始 -->
    <form class="layui-form">
        <div class="layui-form-item">
            <?php if(is_array($auth_list)): $i = 0; $__LIST__ = $auth_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(in_array($vo['id'],$user_auth)): ?><input type="checkbox" name="auth_id[]" title="<?php echo ($vo["username"]); ?>" value="<?php echo ($vo["id"]); ?>" checked="checked">
                    <?php else: ?>
                    <input type="checkbox" name="auth_id[]" title="<?php echo ($vo["username"]); ?>" value="<?php echo ($vo["id"]); ?>"><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <input type="hidden" name="id" value="<?php echo ($id); ?>">
            <input type="hidden" name="type" value="<?php echo ($type); ?>">
            <button class="layui-btn" lay-filter="auth" lay-submit="auth" type="submit">
                授权
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
        form.on('submit(auth)', function (data) {
            var _this = $(this);
            _this.attr('disabled', true);
            var url = "<?php echo U('User/userAuth');?>";
            $.post(url, data.field, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.parent.location.href = "<?php echo U('User/index');?>"
                    });
                } else {
                    _this.removeAttr('disabled');
                    layer.msg(res.info);
                }
            });
            return false;
        });
    </script>

</body>
</html>