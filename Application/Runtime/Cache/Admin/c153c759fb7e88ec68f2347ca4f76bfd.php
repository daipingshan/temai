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
    <!-- ECharts单文件引入 -->
    <script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
    <link rel="stylesheet" href="/Public/Admin/css/item_box.css">
</head>
<body>
<!-- 中部开始 -->
<div class="layui-container" style="margin: 15px;width: 98%">
    <fieldset class="layui-elem-field">
        <legend>文章阅读量走势</legend>
        <div class="layui-field-box">
            <div id="count" style="height:400px; width:100%;margin: 0 3%"></div>
        </div>
    </fieldset>

    <fieldset class="layui-elem-field">
        <legend>文章增长量量走势</legend>
        <div class="layui-field-box">
            <div id="num" style="height:400px; width:100%;margin: 0 3%"></div>
        </div>
    </fieldset>
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
    // 基于准备好的dom，初始化echarts图表
    var count_chart = echarts.init(document.getElementById('count'));
    var num_chart = echarts.init(document.getElementById('num'));

    count_option = {
        title: {
            text: '文章阅读量区域图'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        legend: {
            data: ['<?php echo ($title); ?>']
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: <?php echo ($time_data); ?>
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
            {
                name: '<?php echo ($title); ?>',
                type: 'line',
                stack: '总量',
                areaStyle: {},
                data: <?php echo ($count_data); ?>
            },
        ]
    };
    num_option = {
        title: {
            text: '文章增长量区域图'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        legend: {
            data: ['<?php echo ($title); ?>']
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: <?php echo ($time_data); ?>
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
            {
                name: '<?php echo ($title); ?>',
                type: 'line',
                stack: '总量',
                areaStyle: {},
                data: <?php echo ($num_data); ?>
            },
        ]
    };

    // 为echarts对象加载数据
    count_chart.setOption(count_option);
    // 为echarts对象加载数据
    num_chart.setOption(num_option);
</script>
<!-- 中部结束 -->
</body>
</html>