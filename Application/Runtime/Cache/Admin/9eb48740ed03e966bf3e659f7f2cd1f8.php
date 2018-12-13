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
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <?php if(!empty($error_info)): ?><fieldset class="layui-elem-field">
                        <legend>错误信息</legend>
                        <div class="layui-field-box">
                            <?php echo ($error_info); ?>
                        </div>
                    </fieldset><?php endif; ?>
                <?php if(!empty($data)): ?><xblock>
                        <div class="layui-row">
                            <table class="layui-table" lay-size="sm">
                                <colgroup>
                                    <col width="300">
                                    <col width="150">
                                    <?php if(is_array($time_data)): $i = 0; $__LIST__ = $time_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><col><?php endforeach; endif; else: echo "" ;endif; ?>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>标题</th>
                                    <th>发文时间</th>
                                    <?php if(is_array($time_data)): $i = 0; $__LIST__ = $time_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><th><?php echo ($row); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><tr>
                                        <td><a href="http://www.toutiao.com/a<?php echo ($row["article_id"]); ?>" target="_blank"><?php echo ($row["title"]); ?></if></a>
                                        </td>
                                        <td><?php echo ($row["behot_time"]); ?></td>
                                        <?php if(is_array($time_data)): $i = 0; $__LIST__ = $time_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><td><?php echo ($row[$val]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
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
<!-- 中部结束 -->
</body>
</html>