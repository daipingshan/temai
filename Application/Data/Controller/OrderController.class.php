<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/15
 * Time: 13:17
 */

namespace Data\Controller;

/**
 * Class OrderController
 *
 * @package Data\Controller
 */
class OrderController extends CommonController {

    /**
     * 订单抓取
     */
    public function index() {
        $return_data = array('code' => 0, 'msg' => 'error');
        $cookie      = I('post.json', '', 'trim');
        if (empty($cookie)) {
            $return_data['msg'] = 'cookie不能为空！';
            $this->_addLogs('sale_order.log', $return_data);
            die(json_encode($return_data));
        }
        $start_time = date('Y-m-d', strtotime('-3 days'));
        $end_time   = date('Y-m-d');
        $page       = 1;
        $error_msg  = null;
        $data       = $add_data = array();
        while (true) {
            $url = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time={$start_time}&end_time={$end_time}&order_status=&time_type=0&pageNumber={$page}&pageSize=80";
            $res = $this->_get($url, array(), $cookie);
            if ($res === false) {
                $error_msg = '该账号cookie已过期，请更新后再采集！';
                break;
            }
            $result = json_decode($res, true);
            if (empty($result)) {
                $error_msg = '获取数据失败，请重新操作！';
                break;
            }
            foreach ($result as $key => $val) {
                if (is_numeric($key)) {
                    unset($val['id']);
                    $val['article_title']   = trim($val['article_title']);
                    $val['order_status']    = trim($val['order_status']);
                    $goods_id               = get_word($val['commodity_info'], 'id=', '&');
                    $val['goods_id']        = $goods_id;
                    $data[$val['order_id']] = $val;
                }
            }
            if (count($result) < 80) {
                break;
            }
            $page++;
        }
        if ($error_msg) {
            $return_data['msg'] = $error_msg;
            $this->_addLogs('sale_order.log', $return_data);
            die(json_encode($return_data));
        }
        $order_id_key = array_keys($data);
        $order_data   = M('fxg')->where(array('order_id' => array('in', $order_id_key)))->getField('order_id,order_status');
        $model        = M();
        $model->startTrans();
        try {
            foreach ($data as $v) {
                if (isset($order_data[$v['order_id']])) {
                    if ($v['order_status'] != $order_data[$v['order_id']]) {
                        M('fxg_test')->where(array('order_id' => $v['order_id']))->save($v);
                    }
                } else {
                    $news_info = M('tmnews')->where(array('title' => $v['article_title']))->find();
                    if ($news_info) {
                        $v['name']    = $news_info['name'];
                        $v['news_id'] = $news_info['id'];
                        $v['user_id'] = $news_info['user_id'];
                    } else {
                        $v['name']    = '待认领';
                        $v['news_id'] = 0;
                        $v['user_id'] = 0;
                    }
                    $add_data[] = $v;
                }
            }
            $add_count    = count($add_data);
            $update_count = count($data) - $add_count;
            if ($add_count > 0) {
                M('fxg')->addAll($add_data);
            }
            $model->commit();
            $return_data['code'] = 1;
            $return_data['msg']  = "操作成功，更新{$update_count}条，添加{$add_count}条!";
            $this->_addLogs('sale_order.log', $return_data);
            die(json_encode($return_data));
        } catch (\Exception $e) {
            $model->rollback();
            $return_data['msg'] = $e->getMessage();
            $this->_addLogs('sale_order.log', $return_data);
            die(json_encode($return_data));
        }
    }

    /**
     * 放心购订单
     */
    public function fxgOrder() {
        $url          = 'https://fxg.jinritemai.com/order/torder/searchlist';
        $time         = time() * 1000;
        $start_time   = '2018-06-22';
        $end_time     = date('Y-m-d');
        $page         = 0;
        $page_size    = 100;
        $account_info = M('account', 'ht_', 'FXG_DB')->find(1);
        $param        = array('start_time' => $start_time, 'end_time' => $end_time, 'order' => 'create_time', 'is_desc' => 'asc', 'page' => $page, 'pageSize' => $page_size, '__token' => $account_info['account_token'], '_' => $time);
        $cookie       = $account_info['account_cookie'];
        $is_get       = true;
        $get_num      = 0;
        while ($is_get) {
            $param['page'] = $page;
            $res           = $this->_get($url, $param, $cookie);
            $data          = json_decode($res, true);
            if (!isset($data['code']) || $data['code'] != 0) {
                $is_get = false;
                $this->_addLogs("fxg_order_fail.log", $data);
            }
            if (count($data['data']) < $page_size) {
                $is_get = false;
            }
            $order_data = array();
            foreach ($data['data'] as $v) {
                $order        = $v['order'];
                $order_status = 0;
                if ($order['order_status'] < 5) {
                    if ($order['order_status'] == 4 && $order['logistics_code']) {
                        $order_status = 9;
                    } else {
                        $order_status = $order['order_status'];
                    }

                } else {
                    if ($order['final_status'] == 5) {
                        $order_status = 5;
                    } elseif ($order['final_status'] == 7 || $order['final_status'] == 11) {
                        $order_status = 6;
                    } elseif ($order['final_status'] == 12 || $order['final_status'] == 24) {
                        $order_status = 7;
                    } else if ($order['final_status'] == 9 || $order['final_status'] == 15) {
                        $order_status = 8;
                    }
                }
                $commission = 0;
                if ($order['c_type'] == 4) {
                    $cos_ratio = M('item', 'ht_', 'FXG_DB')->where(array('item_id' => $order['product_id']))->getField('cos_ratio');
                    if ($cos_ratio > 0) {
                        $commission = $order['total_amount'] / 100 * $cos_ratio;
                    }
                } else {
                    $commission = $order['total_amount'] / 100 * 0.1;
                }
                if (count($order['spec_desc']) == 2) {
                    $product_name = $order['product_name'] . '【' . $order['spec_desc'][0]['name'] . '-' . $order['spec_desc'][0]['value'] . '@' . $order['spec_desc'][1]['name'] . '-' . $order['spec_desc'][1]['value'] . '】';
                } else {
                    $product_name = $order['product_name'] . '【' . $order['spec_desc'][0]['name'] . '-' . $order['spec_desc'][0]['value'] . '】';
                }
                $order_data[$order['order_id']] = array(
                    'order_id'            => $order['order_id'],
                    'spec_desc'           => json_encode($order['spec_desc']),
                    'product_id'          => $order['product_id'],
                    'product_name'        => $product_name,
                    'repeat'              => $order['repeat'] > 0 ? $order['repeat'] : 0,
                    'total_amount'        => $order['total_amount'] / 100,
                    'post_addr'           => json_encode($order['post_addr']),
                    'post_receiver'       => $order['post_receiver'],
                    'post_tel'            => $order['post_tel'],
                    'order_status'        => $order_status,
                    'order_source'        => $order['c_type'],
                    'buy_num'             => $order['combo_num'],
                    'pay_type'            => $order['pay_type'],
                    'pay_time'            => strtotime($order['create_time']),
                    'finish_time'         => $order['op_time'],
                    'update_time'         => $order['update_time'],
                    'logistics_id'        => $order['logistics_id'],
                    'logistics_code'      => $order['logistics_code'],
                    'logistics_time'      => strtotime($order['logistics_time']),
                    'receipt_time'        => $order['receipt_time'],
                    'logistics_detail'    => $order['logistics_detail'] ? json_encode($order['logistics_detail']) : '',
                    'seller_words'        => $order['seller_words'],
                    'buyer_words'         => $order['buyer_words'],
                    'urge_cnt'            => $order['urge_cnt'],
                    'cancel_remark'       => $order['cancel_remark'],
                    'commission'          => $commission,
                    'out_logistics_money' => 18,
                );
            }
            if (count($order_data) > 0) {
                $model = M('order', 'ht_', 'FXG_DB');
                $page++;
                $order_id  = array_keys($order_data);
                $have_data = $model->where(array('order_id' => array('in', $order_id)))->field('id,order_id')->select();
                $model->startTrans();
                try {
                    foreach ($have_data as $order) {
                        unset($order_data[$order['order_id']]['commission'], $order_data[$order['order_id']]['out_logistics_money']);
                        $model->where(array('id' => $order['id']))->save($order_data[$order['order_id']]);
                        unset($order_data[$order['order_id']]);
                    }
                    if (count($order_data) > 0) {
                        $add_data = array_values($order_data);
                        $model->addAll($add_data);
                    }
                    $model->commit();
                    $this->_addLogs("fxg_order_success.log", "第{$page}页订单抓取并处理成功");
                } catch (\Exception $e) {
                    $model->rollback();
                    $this->_addLogs("fxg_order_fail.log", $e->getMessage());
                }
            } else {
                $get_num++;
                if ($get_num > 10) {
                    $is_get = false;
                    $this->_addLogs("fxg_order_fail.log", '请求10次数据为空！');
                }
            }
            sleep(2);
        }
    }
}