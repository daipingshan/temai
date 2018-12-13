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
    <form class="layui-form" autocomplete="off">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>账号名称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="username" style="position: absolute;top: -999px;">
                <input type="text" name="name" autocomplete="off"
                <?php if(!empty($info)): ?>value="<?php echo ($info['name']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入账号名称">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>登录密码
            </label>
            <div class="layui-input-inline">

                <input type="password" style="position: absolute;top: -888px;"/>
                <input type="password" name="password" value="" class="layui-input" placeholder="请输入登录密码"
                       autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>组长账号
            </label>
            <div class="layui-input-inline">
                <input type="text" name="real_name"
                <?php if(!empty($info)): ?>value="<?php echo ($info['zzname']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入组长账号">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>联盟PID
            </label>
            <div class="layui-input-inline">
                <input type="text" name="pid"
                <?php if(!empty($info)): ?>value="<?php echo ($info['pid']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入联盟PID">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>头条ID
            </label>
            <div class="layui-input-inline">
                <input type="text" name="source_id"
                <?php if(!empty($info)): ?>value="<?php echo ($info['source_id']); ?>"<?php endif; ?>
                class="layui-input" placeholder="请输入头条ID">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>账号授权
            </label>
            <div class="layui-input-inline">
                <select name="group_id">
                    <option>请选择账号权限</option>
                    <?php if(is_array($auth_list)): $i = 0; $__LIST__ = $auth_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><option value='<?php echo ($key); ?>'
                        <?php if(!empty($info)): if(($info["group_id"]) == $key): ?>selected<?php endif; endif; ?>
                        ><?php echo ($row["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>外网登录
            </label>
            <div class="layui-input-inline">
                <input type="checkbox" name="is_outer_net" title="是" value="1"
                <?php if(($info["is_outer_net"]) == "1"): ?>checked="checked"<?php endif; ?>
                >
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