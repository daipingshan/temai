<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="<?php echo U('getUrl');?>">
    <textarea type="text" name="data" id="data"></textarea>
    <input type="hidden" name="sign" value="" id="sign"/>
    <button type="button">提交</button>
</form>

<script src="/Public/HaiTao/js/jquery.min.js"></script>
<script type="text/javascript">
    function a9(b, a) {

        return b << a | b >>> 32 - a

    }

    function bf(g, d) {

        var j, c, f, b, h;

        return f = 2147483648 & g,

            b = 2147483648 & d,

            j = 1073741824 & g,

            c = 1073741824 & d,

            h = (1073741823 & g) + (1073741823 & d),

            j & c ? 2147483648 ^ h ^ f ^ b : j | c ? 1073741824 & h ? 3221225472 ^ h ^ f ^ b : 1073741824 ^ h ^ f ^ b : h ^ f ^ b

    }

    function bk(b, a, c) {

        return b & a | ~b & c

    }

    function bb(b, a, c) {

        return b & c | a & ~c

    }

    function bs(b, a, c) {

        return b ^ a ^ c

    }

    function be(b, a, c) {

        return a ^ (b | ~c)

    }

    function ba(h, g, b, i, f, j, d) {

        return h = bf(h, bf(bf(bk(g, b, i), f), d)),

            bf(a9(h, j), g)

    }

    function bq(h, f, b, j, g, k, d) {

        return h = bf(h, bf(bf(bb(f, b, j), g), d)),

            bf(a9(h, k), f)

    }

    function a8(g, b, f, h, d, j, a) {

        return g = bf(g, bf(bf(bs(b, f, h), d), a)),

            bf(a9(g, j), b)

    }

    function bh(j, f, h, b, g, k, d) {

        return j = bf(j, bf(bf(be(f, h, b), g), d)),

            bf(a9(j, k), f)

    }


    function bp(h) {

        for (var l, f = h.length, g = f + 8, b = (g - g % 64) / 64, k = 16 * (b + 1), d = new Array(k - 1), m = 0, j = 0; f > j;) {

            l = (j - j % 4) / 4,

                m = j % 4 * 8,

                d[l] = d[l] | h.charCodeAt(j) << m,

                j++

        }

        return l = (j - j % 4) / 4,

            m = j % 4 * 8,

            d[l] = d[l] | 128 << m,

            d[k - 2] = f << 3,

            d[k - 1] = f >>> 29,

            d

    }


    function bd(d) {

        var b, f, a = "", c = "";

        for (f = 0; 3 >= f; f++) {

            b = d >>> 8 * f & 255,

                c = "0" + b.toString(16),

                a += c.substr(c.length - 2, 2)

        }

        return a

    }


    function bn(c) {

        c = c.replace(/\r\n/g, "\n");

        for (var b = "", d = 0; d < c.length; d++) {

            var a = c.charCodeAt(d);

            128 > a ? b += String.fromCharCode(a) : a > 127 && 2048 > a ? (b += String.fromCharCode(a >> 6 | 192),

                b += String.fromCharCode(63 & a | 128)) : (b += String.fromCharCode(a >> 12 | 224),

                b += String.fromCharCode(a >> 6 & 63 | 128),

                b += String.fromCharCode(63 & a | 128))

        }

        return b

    }


    function encode(bo) {

        var bl, bg, a7, bm, a6, br, a4, a3, G, J = [], bt = 7, aZ = 12, Q = 17, X = 22, bc = 5, a1 = 9, F = 14,
            a5 = 20, L = 4, U = 11, z = 16, bj = 23, bi = 6, a0 = 10, Y = 15, a2 = 21;

        for (bo = bn(bo),

                 J = bp(bo),

                 br = 1732584193,

                 a4 = 4023233417,

                 a3 = 2562383102,

                 G = 271733878,

                 bl = 0; bl < J.length; bl += 16) {

            bg = br,

                a7 = a4,

                bm = a3,

                a6 = G,

                br = ba(br, a4, a3, G, J[bl + 0], bt, 3614090360),

                G = ba(G, br, a4, a3, J[bl + 1], aZ, 3905402710),

                a3 = ba(a3, G, br, a4, J[bl + 2], Q, 606105819),

                a4 = ba(a4, a3, G, br, J[bl + 3], X, 3250441966),

                br = ba(br, a4, a3, G, J[bl + 4], bt, 4118548399),

                G = ba(G, br, a4, a3, J[bl + 5], aZ, 1200080426),

                a3 = ba(a3, G, br, a4, J[bl + 6], Q, 2821735955),

                a4 = ba(a4, a3, G, br, J[bl + 7], X, 4249261313),

                br = ba(br, a4, a3, G, J[bl + 8], bt, 1770035416),

                G = ba(G, br, a4, a3, J[bl + 9], aZ, 2336552879),

                a3 = ba(a3, G, br, a4, J[bl + 10], Q, 4294925233),

                a4 = ba(a4, a3, G, br, J[bl + 11], X, 2304563134),

                br = ba(br, a4, a3, G, J[bl + 12], bt, 1804603682),

                G = ba(G, br, a4, a3, J[bl + 13], aZ, 4254626195),

                a3 = ba(a3, G, br, a4, J[bl + 14], Q, 2792965006),

                a4 = ba(a4, a3, G, br, J[bl + 15], X, 1236535329),

                br = bq(br, a4, a3, G, J[bl + 1], bc, 4129170786),

                G = bq(G, br, a4, a3, J[bl + 6], a1, 3225465664),

                a3 = bq(a3, G, br, a4, J[bl + 11], F, 643717713),

                a4 = bq(a4, a3, G, br, J[bl + 0], a5, 3921069994),

                br = bq(br, a4, a3, G, J[bl + 5], bc, 3593408605),

                G = bq(G, br, a4, a3, J[bl + 10], a1, 38016083),

                a3 = bq(a3, G, br, a4, J[bl + 15], F, 3634488961),

                a4 = bq(a4, a3, G, br, J[bl + 4], a5, 3889429448),

                br = bq(br, a4, a3, G, J[bl + 9], bc, 568446438),

                G = bq(G, br, a4, a3, J[bl + 14], a1, 3275163606),

                a3 = bq(a3, G, br, a4, J[bl + 3], F, 4107603335),

                a4 = bq(a4, a3, G, br, J[bl + 8], a5, 1163531501),

                br = bq(br, a4, a3, G, J[bl + 13], bc, 2850285829),

                G = bq(G, br, a4, a3, J[bl + 2], a1, 4243563512),

                a3 = bq(a3, G, br, a4, J[bl + 7], F, 1735328473),

                a4 = bq(a4, a3, G, br, J[bl + 12], a5, 2368359562),

                br = a8(br, a4, a3, G, J[bl + 5], L, 4294588738),

                G = a8(G, br, a4, a3, J[bl + 8], U, 2272392833),

                a3 = a8(a3, G, br, a4, J[bl + 11], z, 1839030562),

                a4 = a8(a4, a3, G, br, J[bl + 14], bj, 4259657740),

                br = a8(br, a4, a3, G, J[bl + 1], L, 2763975236),

                G = a8(G, br, a4, a3, J[bl + 4], U, 1272893353),

                a3 = a8(a3, G, br, a4, J[bl + 7], z, 4139469664),

                a4 = a8(a4, a3, G, br, J[bl + 10], bj, 3200236656),

                br = a8(br, a4, a3, G, J[bl + 13], L, 681279174),

                G = a8(G, br, a4, a3, J[bl + 0], U, 3936430074),

                a3 = a8(a3, G, br, a4, J[bl + 3], z, 3572445317),

                a4 = a8(a4, a3, G, br, J[bl + 6], bj, 76029189),

                br = a8(br, a4, a3, G, J[bl + 9], L, 3654602809),

                G = a8(G, br, a4, a3, J[bl + 12], U, 3873151461),

                a3 = a8(a3, G, br, a4, J[bl + 15], z, 530742520),

                a4 = a8(a4, a3, G, br, J[bl + 2], bj, 3299628645),

                br = bh(br, a4, a3, G, J[bl + 0], bi, 4096336452),

                G = bh(G, br, a4, a3, J[bl + 7], a0, 1126891415),

                a3 = bh(a3, G, br, a4, J[bl + 14], Y, 2878612391),

                a4 = bh(a4, a3, G, br, J[bl + 5], a2, 4237533241),

                br = bh(br, a4, a3, G, J[bl + 12], bi, 1700485571),

                G = bh(G, br, a4, a3, J[bl + 3], a0, 2399980690),

                a3 = bh(a3, G, br, a4, J[bl + 10], Y, 4293915773),

                a4 = bh(a4, a3, G, br, J[bl + 1], a2, 2240044497),

                br = bh(br, a4, a3, G, J[bl + 8], bi, 1873313359),

                G = bh(G, br, a4, a3, J[bl + 15], a0, 4264355552),

                a3 = bh(a3, G, br, a4, J[bl + 6], Y, 2734768916),

                a4 = bh(a4, a3, G, br, J[bl + 13], a2, 1309151649),

                br = bh(br, a4, a3, G, J[bl + 4], bi, 4149444226),

                G = bh(G, br, a4, a3, J[bl + 11], a0, 3174756917),

                a3 = bh(a3, G, br, a4, J[bl + 2], Y, 718787259),

                a4 = bh(a4, a3, G, br, J[bl + 9], a2, 3951481745),

                br = bf(br, bg),

                a4 = bf(a4, a7),

                a3 = bf(a3, bm),

                G = bf(G, a6)

        }

        var V = bd(br) + bd(a4) + bd(a3) + bd(G);

        return V.toLowerCase()

    }

    $('button').click(function () {
        $('#sign').val(encode($('#data').val()));
        $('form').submit();
    })
</script>
</body>
</html>