<?php
namespace Home\Controller;

use Common\Controller\CommonBaseController;
use Think\Controller;

class IndexController extends CommonBaseController {
    public function index() {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }

    public function test() {
        $url = "https://fxg.jinritemai.com/order/torder/searchlist?order_id=&post_tel=&post_receiver=&shop_name=&product_name=&start_time=&end_time=&start_receipt_time=&end_receipt_time=&final_status=2&order=&is_desc=&sort_changed=1&c_type=&logistics_id=&logistics_code=&urge_tag=0&pay_type=&biz_type=&v_type=&page=0&pageSize=20&business_type=4&__token=bc44142ead95247f13f27ed111c847ec&_=1528248078046";
        $param = array();
        $cookie = "_ga=GA1.2.1362264027.1512698213; _gid=GA1.2.899203609.1528181367; UM_distinctid=163cee2754f154-0536c15126f3c2-b34356b-144000-163cee275504a6; PHPSESSID=qvgdch7se8s7kd2t33ejii4hd7; _gat_gtag_UA_101725298_1=1";
        $data = $this->_get($url,$param,$cookie);
        echo $data;exit;
    }
}