<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>特卖管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/font.css?v=<?php echo C('CSS_VER');?>">
    
    <script src="/Public/Admin/lib/layui/layui.all.js?v=<?php echo C('JS_VER');?>" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
    <link rel="stylesheet" href="/Public/Admin/css/item_box.css">
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px;width: 98%">
    <div class="layui-tab">
        <ul class="layui-tab-title get-url">
            <li
            <?php if(($type) == "one"): ?>class="layui-this"<?php endif; ?>
            data-url="<?php echo U('Article/index');?>">近3小时文章走势</li>
            <li
            <?php if(($type) == "two"): ?>class="layui-this"<?php endif; ?>
            data-url="<?php echo U('Article/articleList');?>">文章走势筛选</li>
            <li
            <?php if(($type) == "three"): ?>class="layui-this"<?php endif; ?>
            data-url="<?php echo U('Article/numList');?>">文章增长量筛选</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <?php if($type != 'one'): ?><form class="layui-form" name="search">
                        <div class="layui-form-pane">
                            <div class="layui-form-item">
                                <div class="layui-input-inline">
                                    <input type="text" name="title" placeholder="请输入文章关键字" autocomplete="off"
                                           class="layui-input" value="<?php echo I('get.title');?>">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" name="article_id" placeholder="请输入文章编号" autocomplete="off"
                                           class="layui-input" value="<?php echo I('get.article_id');?>">
                                </div>
                                <div class="layui-input-inline" style="width: 300px">
                                    <input type="text" name="send_time" class="layui-input" placeholder="请选择发文时间范围"
                                           id="send-time"
                                           value="<?php echo I('get.send_time','','trim,urldecode');?>">
                                </div>
                                <div class="layui-input-inline" style="width: 300px;">
                                    <input type="text" name="create_time" class="layui-input" placeholder="请选择采集时间范围"
                                           id="create-time"
                                           value="<?php echo I('get.create_time','','trim,urldecode');?>">
                                </div>
                                <div class="layui-input-inline" style="width:80px">
                                    <input type="hidden" name="media_id" id="media_id" value="<?php echo I('get.media_id');?>">
                                    <button class="layui-btn"><i
                                            class="layui-icon">
                                        &#xe615;</i></button>
                                </div>
                            </div>
                        </div>
                    </form><?php endif; ?>
                <xblock>
                    <button data-id=""
                    <?php if(I('get.media_id')): ?>class="layui-btn layui-btn-sm item-cate default-bgcolor"
                        <?php else: ?>
                        class="layui-btn layui-btn-sm item-cate"<?php endif; ?>
                    >全部</button>
                    <?php if(is_array($media_id)): foreach($media_id as $k=>$row): ?><button data-id="<?php echo ($k); ?>"
                        <?php if(I('get.media_id') != $k): ?>class="layui-btn layui-btn-sm item-cate
                            default-bgcolor"
                            <?php else: ?>
                            class="layui-btn layui-btn-sm item-cate"<?php endif; ?>
                        ><?php echo ($row); ?></button><?php endforeach; endif; ?>
                </xblock>
                <?php if(!empty($error_info)): ?><fieldset class="layui-elem-field">
                        <legend>错误信息</legend>
                        <div class="layui-field-box">
                            <?php echo ($error_info); ?>
                        </div>
                    </fieldset><?php endif; ?>
                <?php if(!empty($data)): ?><xblock>
                        <div class="layui-row" style="height: 500px; overflow-y:scroll;">
                            <table class="layui-table">
                                <thead>
                                <tr>
                                    <th>标题</th>
                                    <th>分类</th>
                                    <th>发文时间</th>
                                    <?php if(is_array($key_data)): $i = 0; $__LIST__ = $key_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><th><?php echo ($row); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><tr>
                                        <td><a href="http://www.toutiao.com/a<?php echo ($row["article_id"]); ?>" target="_blank"><?php echo ($row["title"]); if($row['is_refresh'] == 1): ?><span style="color: red">【刷阅读量】</span><?php endif; ?></a>
                                        </td>
                                        <td><?php echo ($row["type_name"]); ?></td>
                                        <td><?php echo ($row["send_time"]); ?></td>
                                        <?php if(is_array($key_data)): $i = 0; $__LIST__ = $key_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><td><?php echo ($row[$val]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <td><a href="<?php echo U('Article/detail',array('article_id'=>$row['article_id']));?>"
                                               class="layui-btn layui-btn-xs" target="_blank">查看走势</a></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo ($page); ?>
                    </xblock><?php endif; ?>

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
    //日期时间范围
    laydate.render({
        elem: '#send-time'
        , type: 'datetime'
        , range: '~'
    });
    //日期时间范围
    laydate.render({
        elem: '#create-time'
        , type: 'datetime'
        , range: '~'
    });
    $('.item-cate').click(function () {
        var media_id = $(this).data('id');
        var type = "<?php echo ($type); ?>";
        if (type == 'one') {
            location.href = "<?php echo U('Article/index');?>?media_id=" + media_id;
        } else {
            $('#media_id').val(media_id)
            $('form[name=search]').submit();
        }
    });
    $('.get-url li').on('click', function () {
        location.href = $(this).data('url');
    })
</script>
<!-- 中部结束 -->
</body>
</html>