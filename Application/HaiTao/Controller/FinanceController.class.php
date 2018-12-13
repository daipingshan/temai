<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/27
 * Time: 16:15
 */

namespace HaiTao\Controller;

/**
 * 财务数据
 * Class FinanceController
 *
 * @package HaiTao\Controller
 */
class FinanceController extends CommonController {


    /**
     * 订单利润
     */
    public function index() {
        $pay_time          = I('get.pay_time', '', 'trim');
        $finish_time       = I('get.finish_time', '', 'trim');
        $where             = array('order_status' => array('in', '5,7,8,9'));
        $order_status_view = array(5 => '已完成', 7 => '退货成功', 8 => '退货失败', 9 => '拒收|异常');
        $order             = 'id asc';
        $money             = 0;
        if (!empty($pay_time) || !empty($finish_time)) {
            if (!empty($pay_time)) {
                list($start_time, $end_time) = explode(' ~ ', $pay_time);
                if ($start_time && $end_time) {
                    $start_time        = strtotime($start_time);
                    $end_time          = strtotime($end_time);
                    $where['pay_time'] = array('between', array($start_time, $end_time));
                }
                $order = 'pay_time asc';
            }
            if (!empty($finish_time)) {
                list($start_time, $end_time) = explode(' ~ ', $finish_time);
                if ($start_time && $end_time) {
                    $start_time           = strtotime($start_time);
                    $end_time             = strtotime($end_time);
                    $where['finish_time'] = array('between', array($start_time, $end_time));
                }
                $order = 'finish_time asc';
            }
            $model = M('order');
            if (IS_AJAX) {
                $field = "order_id,order_status,product_id,product_name,pay_time,finish_time,buy_num,total_amount,supple_price,get_logistics_money,commission,out_logistics_money,spec_desc";
                $data  = $model->field($field)->where($where)->order($order)->select();
                foreach ($data as &$val) {
                    $val['order_status_view'] = $order_status_view[$val['order_status']];
                    $val['pay_time']          = date('Y-m-d H:i:s', $val['pay_time']);
                    $val['finish_time']       = date('Y-m-d H:i:s', $val['finish_time']);
                    if ($val['order_status'] == 5 || $val['order_status'] == 8) {
                        if ($val['commission'] > 0 && $val['out_logistics_money'] > 0 && $val['supple_price'] > 0) {
                            $tmp_money                  = $val['total_amount'] > 200 ? $val['total_amount'] * 0.03 : 1;
                            $tmp_money                  = round($tmp_money, 2);
                            $val['tmp_money']           = $tmp_money;
                            $income                     = $val['total_amount'] - ($val['supple_price'] * $val['buy_num']) - $val['commission'] - $val['out_logistics_money'] - $tmp_money;
                            $val['money']               = $income;
                            $val['order_status_remark'] = '<span style="color: #00F7DE">正常</span>';;
                            $money += $income;
                        } else {
                            $val['order_status_remark'] = '<span style="color: red">异常</span>';
                            $val['money']               = 0;
                            $val['tmp_money']           = 0;
                        }
                    } else {
                        if ($val['out_logistics_money'] > 0 && $val['get_logistics_money'] > 0) {
                            $val['tmp_money']           = 0;
                            $income                     = $val['out_logistics_money'] + $val['get_logistics_money'];
                            $val['money']               = '-' . $income;
                            $val['order_status_remark'] = '<span style="color: #00F7DE">正常</span>';
                            $money                      -= $income;
                        } else {
                            $val['order_status_remark'] = '<span style="color: red">异常</span>';
                            $val['money']               = 0;
                            $val['tmp_money']           = 0;
                        }
                    }
                }
                $this->output($data, $money);
            }
        } else {
            if (IS_AJAX) {
                $this->output(array(), $money);
            }
        }
        $this->display();
    }

    /**
     * 下载数据
     */
    public function downData() {
        $pay_time          = I('get.pay_time', '', 'trim');
        $finish_time       = I('get.finish_time', '', 'trim');
        $where             = array('order_status' => array('in', '5,7,8,9'));
        $order_status_view = array(5 => '已完成', 7 => '退货成功', 8 => '退货失败', 9 => '拒收|异常');
        $order             = 'id asc';
        if (!empty($pay_time) || !empty($finish_time)) {
            if (!empty($pay_time)) {
                list($start_time, $end_time) = explode(' ~ ', $pay_time);
                if ($start_time && $end_time) {
                    $start_time        = strtotime($start_time);
                    $end_time          = strtotime($end_time);
                    $where['pay_time'] = array('between', array($start_time, $end_time));
                }
                $order = 'pay_time asc';
            }
            if (!empty($finish_time)) {
                list($start_time, $end_time) = explode(' ~ ', $finish_time);
                if ($start_time && $end_time) {
                    $start_time           = strtotime($start_time);
                    $end_time             = strtotime($end_time);
                    $where['finish_time'] = array('between', array($start_time, $end_time));
                }
                $order = 'finish_time asc';
            }
            $model = M('order');
            $field = "order_id,order_status,product_id,product_name,pay_time,finish_time,buy_num,total_amount,supple_price,get_logistics_money,commission,out_logistics_money,spec_desc";
            $data  = $model->field($field)->where($where)->order($order)->select();
            foreach ($data as &$val) {
                $val['order_status_view'] = $order_status_view[$val['order_status']];
                $val['pay_time']          = date('Y-m-d H:i:s', $val['pay_time']);
                $val['finish_time']       = date('Y-m-d H:i:s', $val['finish_time']);
                if ($val['order_status'] == 5 || $val['order_status'] == 8) {
                    if ($val['commission'] > 0 && $val['out_logistics_money'] > 0 && $val['supple_price'] > 0) {
                        $tmp_money                  = $val['total_amount'] > 200 ? $val['total_amount'] * 0.03 : 1;
                        $tmp_money                  = round($tmp_money, 2);
                        $val['tmp_money']           = $tmp_money;
                        $income                     = $val['total_amount'] - ($val['supple_price'] * $val['buy_num']) - $val['commission'] - $val['out_logistics_money'] - $tmp_money;
                        $val['money']               = $income;
                        $val['order_status_remark'] = '正常';
                    } else {
                        $val['order_status_remark'] = '异常';
                        $val['money']               = 0;
                        $val['tmp_money']           = 0;
                    }
                } else {
                    if ($val['out_logistics_money'] > 0 && $val['get_logistics_money'] > 0) {
                        $val['tmp_money']           = 0;
                        $income                     = $val['out_logistics_money'] + $val['get_logistics_money'];
                        $val['money']               = '-' . $income;
                        $val['order_status_remark'] = '正常';
                    } else {
                        $val['order_status_remark'] = '异常';
                        $val['money']               = 0;
                        $val['tmp_money']           = 0;
                    }
                }
            }
            $key_name  = array(
                'order_id'            => '订单编号',
                'product_name'        => '商品名称',
                'buy_num'             => '购买数量',
                'total_amount'        => '支付金额',
                'pay_time'            => '下单时间',
                'finish_time'         => '完成时间',
                'order_status_view'   => '订单状态',
                'order_status_remark' => '数据状态',
                'supple_price'        => '供货价',
                'commission'          => '达人服务费',
                'tmp_money'           => '快递代收手续费',
                'out_logistics_money' => '寄件运费',
                'get_logistics_money' => '退件运费',
                'money'               => '利润',
            );
            $file_name = '放心购利润';
            if ($pay_time) {
                $file_name = "放心购-下单时间{$pay_time}-利润";
            }
            if ($finish_time) {
                $file_name = "放心购-完成时间{$finish_time}-利润";
            }
            download_xls($data, $key_name, $file_name);
        }
    }
}