/**
 * Created by daipingshan on 2018/5/7.
 */
var is_add = true;
$('body').on('click', '.addItemCache', function () {
    if (is_add === false) {
        layer.msg('正在请求中...，操作太频繁，请稍后再试！');
        return false;
    }
    var item_num = parseInt($('#cart .cart-num').text());
    if (item_num >= 20) {
        layer.msg('最多只能添加20件商品！');
        return false;
    }
    var _this = $(this);
    var item_id = _this.data('id');
    var type = _this.data('type');
    var shopp = _this.data('shopp');
    var post_data = _this.data('val');
    var url = _this.data('url');
    is_add = false;
    $.post(url, {id: item_id, post_data: post_data, type: type, shopp: shopp}, function (res) {
        if (res.status == 1) {
            if (res.info.fee || res.info.ratio) {
                var html = '<div class="layui-row float-box" style="font-size: 10px;line-height: 15px;height: 15px">佣金比率：' + res.info.ratio + '&nbsp;预估：' + res.info.fee + '</div><div class="layui-row float-box" style="font-size: 10px;line-height: 15px;height: 15px">小店商品：' + res.info.type_name + '&nbsp;月销量：' + res.info.month_sell_num + '</div>';
                _this.parents('.item-box').find('.json-callback').html(html);
            }
            var img = _this.parents('.item-box').find('.img img');
            $('#cart .cart-num').text(item_num + 1);
            _this.parents('.float-box').find('.add-box').addClass('layui-hide');
            _this.parents('.float-box').find('.del-box').removeClass('layui-hide');
            flyCart(img);
        } else {
            layer.msg(res.info);
        }
        is_add = true;
    });
});

$('.temai-preview').click(function () {
    var num = $('#cart .cart-num').text();
    if (num == 0) {
        layer.msg('请先添加商品');
        return false;
    }
    x_admin_show($(this).data('title'), $(this).data('url'), $(this).data('width'), $(this).data('height'));
});

$('body').on('click', '.del-item', function () {
    var _this = $(this);
    var url = $(this).data('url');
    var id = $(this).data('id');
    layer.confirm('您确定要删除该选品吗？', function () {
        $.post(url, {id: id}, function (res) {
            layer.msg(res.info);
            if (res.status == 1) {
                _this.parents('.float-box').find('.del-box').addClass('layui-hide');
                _this.parents('.float-box').find('.add-box').removeClass('layui-hide');
                var item_num = parseInt($('#cart .cart-num').text());
                $('#cart .cart-num').text(item_num - 1);
            }
        })
    });
});


/**
 * 飞入购物车
 * @param imgtodrag
 */
function flyCart(imgtodrag) {
    var cart = $('#cart');
    if (imgtodrag) {
        var imgclone = imgtodrag.clone()
            .offset({
                top: imgtodrag.offset().top,
                left: imgtodrag.offset().left
            })
            .css({
                'opacity': '0.5',
                'position': 'absolute',
                'height': '150px',
                'width': '150px',
                'z-index': '100'
            })
            .appendTo($('body'))
            .animate({
                'top': cart.offset().top + 10,
                'left': cart.offset().left + 10,
                'width': 75,
                'height': 75
            }, 1000, 'easeInOutExpo');

        imgclone.animate({
            'width': 0,
            'height': 0
        }, function () {
            $(this).detach()
        });
    }
}