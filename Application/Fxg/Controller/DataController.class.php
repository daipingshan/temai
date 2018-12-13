<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/11/1
 * Time: 14:03
 */

namespace Fxg\Controller;


class DataController extends CommonController {
    protected $order_source_list = array(
        1  => '付费-广告',
        2  => '付费-其他付费流量',
        3  => '付费-移动互联',
        4  => '付费-自媒体',
        5  => '付费-放心购频道',
        6  => '付费-广告拼团',
        7  => '免费-营销卡片',
        8  => '免费-后台创建',
        9  => '免费-其他平台自费推广',
        10 => '免费-其他',
        11 => '免费-微头条',
        12 => '免费-闪购',
    );


    /**
     * @var bool
     */
    protected $checkUser = false;


    /**
     * 自动采集商品
     */
    public function getItems() {
        $shop_data = M('shop')->select();
        $data      = $add_data = array();
        $method    = "product.list";
        foreach ($shop_data as $val) {
            $item_data = $product_id_data = array();
            $param     = array('page' => 0, 'size' => 100);
            $is_have   = true;
            while ($is_have) {
                $res = $this->_getOpenData($val['id'], $param, $method);
                if ($res['status'] == 0) {
                    $is_have  = false;
                    $log_data = array('time' => date('Y-m-d H:i:s'), 'name' => $val['shop_name'], 'res' => $res);
                    $this->_addLogs('fxg_get_item.log', $log_data);
                    continue;
                }
                if ($res['data']['count'] < 100) {
                    $is_have = false;
                }
                $param['page'] = $param['page'] + 1;
                if ($res['data']['data']) {
                    $item_data = array_merge($item_data, $res['data']['data']);
                }
                foreach ($item_data as $item) {
                    $item['shop_id']          = $val['id'];
                    $item['market_price']     = $item['market_price'] / 100;
                    $item['discount_price']   = $item['discount_price'] / 100;
                    $item['settlement_price'] = $item['settlement_price'] / 100;
                    $item_id                  = $item['product_id_str'];
                    $item['product_id']       = $item_id;
                    unset($item['product_id_str']);
                    $data[$item_id] = $item;
                }
            }
        }
        if (count($data) > 0) {
            $product_id_data = array_keys($data);
            $have_items      = M('item')->where(array('product_id' => array('in', $product_id_data)))->select();
            foreach ($have_items as $row) {
                M('item')->where(array('id' => $row['id']))->save($data[$row['product_id']]);
                unset($data[$row['product_id']]);
            }
            $add_data = array_chunk(array_values($data), 999);
            foreach ($add_data as $temp) {
                M('item')->addAll($temp);
            }
        }
    }

    /**
     * 自动采集订单
     */
    public function getOrders() {
        $type          = I('get.type', 'now', 'trim');
        $shop_data     = M('shop')->select();
        $pay_type_data = array('货到付款', '微信', '支付宝');
        $method        = "order.list";
        foreach ($shop_data as $val) {
            $order_data = $order_id_data = $data = $add_data = array();
            if ($type == 'now') {
                $start_time = date('Y-m-d', strtotime('-2 days')) . " 00:00:00";
            } else {
                $start_time = date('Y-m-d', strtotime('-1 months')) . " 00:00:00";
            }
            $param   = array('page' => 0, 'size' => 100, 'start_time' => $start_time, 'order_by' => 'create_time');
            $is_have = true;
            while ($is_have) {
                $res = $this->_getOpenData($val['id'], $param, $method);
                if ($res['status'] == 0) {
                    $is_have  = false;
                    $log_data = array('time' => date('Y-m-d H:i:s'), 'name' => $val['shop_name'], 'res' => $res);
                    $this->_addLogs('fxg_get_order.log', $log_data);
                    continue;
                }
                if ($res['data']['count'] < 100) {
                    $is_have = false;
                }
                $param['page'] = $param['page'] + 1;
                if ($res['data']['list']) {
                    $order_data = array_merge($order_data, $res['data']['list']);
                }
            }
            foreach ($order_data as $order) {
                foreach ($order['child'] as $order_info) {
                    $order_info['order_shop_id']  = $order_info['shop_id'];
                    $order_info['shop_id']        = $val['id'];
                    $order_info['pay_type_name']  = $pay_type_data[$order_info['pay_type']];
                    $order_info['type_name']      = $this->order_source_list[$order_info['c_type']];
                    $order_info['spec_desc']      = json_encode($order_info['spec_desc']);
                    $order_info['post_addr']      = json_encode($order_info['post_addr']);
                    $order_info['campaign_info']  = json_encode($order_info['campaign_info']);
                    $order_info['coupon_info']    = json_encode($order_info['coupon_info']);
                    $order_info['total_amount']   = $order_info['total_amount'] > 0 ? $order_info['total_amount'] / 100 : 0;
                    $order_info['coupon_amount']  = $order_info['coupon_amount'] > 0 ? $order_info['coupon_amount'] / 100 : 0;
                    $order_info['combo_amount']   = $order_info['combo_amount'] > 0 ? $order_info['combo_amount'] / 100 : 0;
                    $order_info['post_amount']    = $order_info['post_amount'] > 0 ? $order_info['post_amount'] / 100 : 0;
                    if ($order_info['c_type'] == 4) {
                        $order_info['commission'] = $order_info['cos_ratio'] > 0 ? ($order_info['total_amount'] + $order_info['post_amount']) * ($order_info['cos_ratio'] / 100) : 0;
                    } else if ($order_info['c_type'] == 2 || $order_info['c_type'] == 5) {
                        $order_info['commission'] = ($order_info['total_amount'] + $order_info['post_amount']) * 0.1;
                    } else {
                        $order_info['commission'] = 0;
                    }
                    $data[$order_info['order_id']] = $order_info;
                }
            }
            if (count($data) > 0) {
                $order_id_data = array_keys($data);
                $have_orders   = M('order')->where(array('order_id' => array('in', $order_id_data)))->select();
                foreach ($have_orders as $row) {
                    M('order')->where(array('id' => $row['id']))->save($data[$row['order_id']]);
                    unset($data[$row['order_id']]);
                }
                $add_data = array_chunk($data, 999);
                foreach ($add_data as $temp) {
                    M('order')->addAll($temp);
                }
            }
        }
    }

    /**
     * 获取快递公司
     */
    public function getLogistics() {
        $method = "order.logisticsCompanyList";
        $param  = array('page' => 0);
        $res    = $this->_getOpenData(1, $param, $method);
        if ($res['status'] == 0) {
            $log_data = array('time' => date('Y-m-d H:i:s'), 'res' => $res);
            $this->_addLogs('fxg_get_logistics.log', $log_data);
            exit();
        }
        $data     = $res['data'];
        $add_data = array();
        foreach ($data as $val) {
            $add_data[$val['id']] = array('logistics_id' => $val['id'], 'name' => $val['name']);
        }
        if (count($add_data) > 0) {
            $logistics_id_data = array_keys($add_data);
            $have_logistics    = M('logistics')->where(array('logistics_id' => array('in', $logistics_id_data)))->select();
            foreach ($have_logistics as $row) {
                M('logistics')->where(array('id' => $row['id']))->save($add_data[$row['logistics_id']]);
                unset($add_data[$row['logistics_id']]);
            }
            if (count($add_data) > 0) {
                $add_temp_data = array_chunk(array_values($add_data), 999);
                foreach ($add_temp_data as $temp) {
                    M('logistics')->addAll($temp);
                }
            }
            S('logistics', null);
        }
    }
}