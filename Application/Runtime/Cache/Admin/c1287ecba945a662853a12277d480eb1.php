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
            <label class="layui-form-label">
                <span class="x-red">*</span>发布账号
            </label>
            <div class="layui-input-block">
                <select class="layui-select" name="account_id">
                    <option value="0">请选择发布账号</option>
                    <?php if(is_array($account)): $i = 0; $__LIST__ = $account;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["username"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <input type="hidden" name="id" value="<?php echo ($id); ?>">
            <button class="layui-btn" lay-filter="auth" lay-submit="auth" type="submit">
                一键发文
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
            if (data.field.account_id < 1) {
                layer.msg('请选择发布账号');
                return false;
            }
            _this.attr('disabled', true);
            var url = "<?php echo U('sendArticle');?>";
            $.post(url, data.field, function (res) {
                if (res.status == 1) {
                    layer.msg(res.info, function () {
                        window.parent.location.href = "<?php echo U('taoJinGeArticle');?>"
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