$(function () {
    //加载弹出层
    layui.use(['form', 'element'],
        function () {
            layer = layui.layer;
            element = layui.element;
        }
    );
    //初如化背景
    function bgint() {
        if (localStorage.bglist) {
            var arr = JSON.parse(localStorage.bglist);//
            //全局背景统一
            if (arr['bgSrc']) {
                $('body,.container,.footer').css('background-image', 'url(' + arr['bgSrc'] + ')');
            }
        }
    }

    bgint();
    //背景主题功能
    var changerlist = new Swiper('.changer-list', {
        initialSlide: 5,
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        coverflow: {
            rotate: 50,
            stretch: -10,
            depth: 100,
            modifier: 1,
            slideShadows: false
        }
    });

    //判断是否显示左侧菜单
    $(window).resize(function () {
        width = $(this).width();
        if (width > 1024) {
            $('#side-nav').show();
        }
    });
    //背景主题设置展示
    is_show_change = true;
    $('#changer-set').click(function (event) {
        if (is_show_change) {
            $('.bg-out').show();
            $('.bg-changer').animate({top: '0px'}, 500);
            is_show_change = false;
        } else {
            $('.bg-changer').animate({top: '-110px'}, 500);
            is_show_change = true;
        }

    });

    //背景主题切换
    $('.bg-changer img.item').click(function (event) {
        if (!localStorage.bglist) {
            arr = {};
        } else {
            arr = JSON.parse(localStorage.bglist);
        }
        var src = $(this).attr('src');
        $('body,.container,.footer').css('background-image', 'url(' + src + ')');
        url = location.href;

        // 单个背景逻辑
        // arr[url]=src;
        // 全局背景统一
        arr['bgSrc'] = src;
        // console.log(arr);

        localStorage.bglist = JSON.stringify(arr);

    });
    //背景初始化
    $('.reset').click(function () {
        localStorage.clear();
        layer.msg('初如化成功', function () {
        });
    });

    //背景切换点击空白区域隐藏
    $('.bg-out').click(function () {
        $('.bg-changer').animate({top: '-110px'}, 500);
        is_show_change = true;
        $(this).hide();
    });

    $('.layui-nav-item').hover(function () {
        $(this).find('dl.layui-nav-child').addClass('layui-show');
        $(this).find('span.layui-nav-more').addClass('layui-nav-mored');
    }, function () {
        $(this).find('dl.layui-nav-child').removeClass('layui-show');
        $(this).find('span.layui-nav-more').removeClass('layui-nav-mored');
    });


    //窄屏下的左侧菜单隐藏效果
    $('.container .open-nav i').click(function (event) {
        $('#side-nav').toggle(400);
        // $('.wrapper .left-nav').toggle(400)
    });

    //左侧菜单效果
    $('.left-nav #nav li').click(function () {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
            $(this).find('.nav_right').html('&#xe697;');
            $(this).children('.sub-menu').slideUp();
            // $(this).siblings().children('.sub-menu').slideUp();
        } else {
            $(this).addClass('open');
            $(this).find('.nav_right').html('&#xe6a6;');
            $(this).children('.sub-menu').slideDown();
            $(this).siblings().children('.sub-menu').slideUp();
            $(this).siblings().removeClass('open');
        }

    })

    $('#full-screen').click(function () {
        if ($(this).data('status') == 0) {
            $(this).data('status', 1);
            fullScreen();
        } else {
            $(this).data('status', 0);
            exitFullScreen();
        }
    });

    $('#full-screen').hover(function () {
        if ($(this).data('status') == 0) {
            layer.tips('全屏显示', '#full-screen', {tips: 1});
        } else {
            layer.tips('关闭全屏显示', '#full-screen', {tips: 1});
        }

    })


    //初始化菜单展开样式
    $('.left-nav #nav li .opened').siblings('a').find('.nav_right').html('&#xe6a6;');
})

/*弹出层*/
/*
 参数解释：
 title   标题
 url     请求的url
 id      需要操作的数据id
 w       弹出层宽度（缺省调默认值）
 h       弹出层高度（缺省调默认值）
 */
function x_admin_show(title, url, w, h) {
    var type = 2;
    if (title == null || title == '') {
        title = false;
    }
    if (url == null || url == '') {
        url = "404.html";
    }
    if (w == null || w == '') {
        w = 800;
    }
    if (h == null || h == '') {
        h = ($(window).height() - 50);
    }
    var area = [w + 'px', h + 'px'];
    if (w.indexOf('%') != -1) {
        area[0] = w;
    }
    if (h.indexOf('%') != -1) {
        area[1] = h;
    }
    layer.open({
        type: type,
        area: area,
        fix: false, //不固定
        maxmin: true,
        shadeClose: true,
        shade: 0.4,
        title: title,
        content: url
    });
}

/*关闭弹出框口*/
function x_admin_close() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}

function fullScreen() {
    var el = document.documentElement;
    var rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen,
        wscript;

    if (typeof rfs != "undefined" && rfs) {
        rfs.call(el);
        return;
    }

    if (typeof window.ActiveXObject != "undefined") {
        wscript = new ActiveXObject("WScript.Shell");
        if (wscript) {
            wscript.SendKeys("{F11}");
        }
    }
}

function exitFullScreen() {
    var el = document;
    var cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullScreen,
        wscript;

    if (typeof cfs != "undefined" && cfs) {
        cfs.call(el);
        return;
    }
    if (typeof window.ActiveXObject != "undefined") {
        wscript = new ActiveXObject("WScript.Shell");
        if (wscript != null) {
            wscript.SendKeys("{F11}");
        }
    }
}