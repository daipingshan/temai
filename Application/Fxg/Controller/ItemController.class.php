<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/11/1
 * Time: 11:36
 */

namespace Fxg\Controller;

use Common\Org\simple_html_dom as Html;
use Common\Org\Http;

class ItemController extends CommonController {

    /**
     * 商品列表
     */
    public function index() {
        if (IS_AJAX) {
            $item_id = I('get.item_id', '', 'trim');
            $keyword = I('get.keyword', '', 'trim');
            $shop_id = I('get.shop_id', '', 'int');
            $status  = I('get.status', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 10, 'int');
            $where   = array();
            if (!empty($item_id)) {
                $where['product_id'] = $item_id;
            }
            if (!empty($keyword)) {
                $where['name'] = array('like', "%{$keyword}%");
            }
            if (!empty($shop_id)) {
                $where['shop_id'] = $shop_id;
            }
            if ($status !== '') {
                $where['status'] = $status;
            }
            $model = M('item');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('create_time desc')->select();
            foreach ($data as &$val) {
                $val['shop_name'] = $this->_getShopCache($val['shop_id'])['shop_name'];
            }
            $this->output($data, $count);
        } else {
            $this->assign('shop_data', $this->_getShop());
            $this->display();
        }
    }

    /**
     * 获取html
     */
    public function getHtml() {
        $this->display();
    }

    public function getUrl() {
        $data = I('get.data', '', 'trim');
        $sign = I('get.sign', '', 'trim');
        $url = "https://h5api.m.taobao.com/h5/mtop.taobao.maserati.xplan.render/1.0/?jsv=2.4.2&appKey=12574478&t=1542446235417&sign={$sign}fa&api=mtop.taobao.maserati.xplan.render&v=1.0&type=originaljsonp&dataType=originaljsonp&timeout=20000&callback=mtopjsonp3&data=%7B%22accountId%22%3A%222771494819%22%2C%22siteId%22%3A41%2C%22pageId%22%3A%226273%22%2C%22fansId%22%3A%222771494819%22%2C%22status%22%3A0%2C%22currentPage%22%3A3%2C%22currentModuleIds%22%3A%22580%2C1031%2C846%2C583%2C565%22%2C%22contentId%22%3A%22211603518215%22%2C%22source%22%3A%22darenhome%22%2C%22channelCode%22%3A%22darenhome%22%2C%22tabString%22%3A%22feeds%22%2C%22beginId%22%3A0%2C%22beginTime%22%3A0%7D";
        echo $url;
    }
}