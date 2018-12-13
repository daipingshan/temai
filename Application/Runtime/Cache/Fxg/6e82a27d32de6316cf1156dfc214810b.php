<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>放心购管理后台</title>
    <link rel="stylesheet" href="/Public/HaiTao/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/HaiTao/css/common.css">
    
</head>
<body class="layui-layout-body" style="left: 0;padding: 20px">

    <!-- 右侧内容框架，更改从这里开始 -->
    <form class="layui-form">
        <div class="layui-form-item">
            <?php if(is_array($auth_list)): $i = 0; $__LIST__ = $auth_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(in_array($vo['id'],$user_auth)): ?><input type="checkbox" name="auth_id[]" title="<?php echo ($vo["name"]); ?>" value="<?php echo ($vo["id"]); ?>" checked="checked">
                    <?php else: ?>
                    <input type="checkbox" name="auth_id[]" title="<?php echo ($vo["name"]); ?>" value="<?php echo ($vo["id"]); ?>"><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <input type="hidden" name="id" value="<?php echo ($id); ?>">
            <button class="layui-btn" lay-filter="auth" lay-submit="auth" type="submit">
                授权
            </button>
        </div>
    </form>
    <!-- 右侧内容框架，更改从这里结束 -->

<script src="/Public/HaiTao/lib/layui/layui.all.js"></script>
<script>
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
        layedit = layui.layedit,
        $ = layui.jquery;
</script>


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