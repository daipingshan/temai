<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>放心购管理后台</title>
    <link rel="stylesheet" href="/Public/HaiTao/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/HaiTao/css/common.css">
    
    <style type="text/css">
        .layui-input-inline {
            width: 60% !important;
        }
    </style>

</head>
<body class="layui-layout-body" style="left: 0;padding: 20px">

    <!-- 右侧内容框架，更改从这里开始 -->
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>账号名称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="username"
                <?php if(!empty($info)): ?>value="<?php echo ($info['username']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入账号名称">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>权限
            </label>
            <div class="layui-input-inline">
                <input type="checkbox" name="is_super" value="1" title="超级权限"
                <?php if($info['is_super'] == 1): ?>checked<?php endif; ?>
                >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>登录密码
            </label>
            <div class="layui-input-inline">
                <input type="password" name="password" value="" class="layui-input" placeholder="请输入登录密码">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <?php if(!empty($info)): ?><input type="hidden" name="id" value="<?php echo ($info['id']); ?>"><?php endif; ?>
            <button class="layui-btn" lay-filter="save" lay-submit="save" type="submit">
                <?php if(($type) == "add"): ?>添加
                    <?php else: ?>
                    编辑<?php endif; ?>
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
        form.on('submit(save)', function (data) {
            var _this = $(this);
            _this.attr('disabled', true);
            var type = "<?php echo ($type); ?>";
            var url = "<?php echo U('User/addAccount');?>";
            if (type == 'update') {
                url = "<?php echo U('User/updateAccount');?>";
            }
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