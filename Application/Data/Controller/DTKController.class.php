<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/5
 * Time: 10:01
 */

namespace Data\Controller;

use Common\Org\Http;

/**
 * 微头条 采集大淘客商品
 * Class DTKController
 *
 * @package Data\Controller
 */
class DTKController extends CommonController {
    /**
     * 添加大淘客商品数据
     */
    public function getDaTaoKeItem() {
        $url  = "http://api.dataoke.com/index.php?r=Port/index&type=paoliang&appkey=4frqyegyob&v=2";
        $Http = new Http();
        $data = json_decode($Http->get($url), true);
        if ($data['data']['total_num'] > 0 && $data['result']) {
            $save_data = $goods_ids = array();
            foreach ($data['result'] as $val) {
                if (strpos($val['Pic'], 'http') === false) {
                    $val['Pic'] = "https:" . $val['Pic'];
                }
                $save_data[] = array(
                    'goods_id'        => $val['GoodsID'],
                    'pic'             => $val['Pic'],
                    'title'           => $val['Title'],
                    'short_title'     => $val['D_title'],
                    'desc'            => $val['Introduce'],
                    'price'           => $val['Org_Price'],
                    'coupon_price'    => $val['Price'],
                    'coupon_money'    => $val['Quan_price'],
                    'commission_rate' => $val['Commission_jihua'] > $val['Commission_queqiao'] ? $val['Commission_jihua'] : $val['Commission_queqiao'],
                    'coupon_url'      => $val['Quan_link'],
                    'add_time'        => date('Ymd'),
                );
                $goods_ids[] = $val['GoodsID'];
            }
            $db_data = M('dtk_goods')->where(array('goods_id' => array('in', $goods_ids), 'add_time' => date('Ymd')))->getField('goods_id', true);
            foreach ($save_data as $k => $v) {
                if (in_array($v['goods_id'], $db_data)) {
                    unset($save_data[$k]);
                }
            }
            if (count($save_data) > 0) {
                M('dtk_goods')->addAll(array_values($save_data));
                $this->_addLogs('dtk.log', date("Y-m-d H:i") . "获取大淘客商品成功，添加数据成功！");
            } else {
                $this->_addLogs('dtk.log', date("Y-m-d H:i") . "获取大淘客商品成功，但所有数据均以添加至商品库！");
            }
        } else {
            $this->_addLogs('dtk.log', date("Y-m-d H:i") . "获取大淘客商品失败！");
        }
    }
}