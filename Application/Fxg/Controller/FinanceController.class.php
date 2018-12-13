<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/27
 * Time: 16:15
 */

namespace Fxg\Controller;

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
        $shop_data    = $this->_getShop();
        $pay_time     = I('get.pay_time', '', 'trim');
        $receipt_time = I('get.receipt_time', '', 'trim');
        $data_status  = I('get.data_status', '', 'trim');
        $shop_id      = I('get.shop_id', 0, 'int');
        $where        = array('order_status' => array('in', '5,9'));
        $order        = 'create_time asc';
        if (!empty($data_status)) {
            $where['product_pay_amount'] = 0;
        }
        if ($shop_id > 0) {
            $where['shop_id'] = $shop_id;
        }
        $order_status_view = array(5 => '已完成', 9 => '退货失败');
        $money             = 0;
        if (!empty($pay_time) || !empty($receipt_time)) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
                $order                = 'pay_time asc';
            }
            list($start_time, $end_time) = explode(' ~ ', $receipt_time);
            if ($start_time && $end_time) {
                $start_time            = strtotime($start_time);
                $end_time              = strtotime($end_time) + 86399;
                $where['receipt_time'] = array('between', array($start_time, $end_time));
                $order                 = 'receipt_time asc';
            }

            $model = M('order');
            if (IS_AJAX) {
                $field = "order_id,shop_id,order_status,product_id,product_name,pay_time,total_amount,commission,product_pay_amount,income,create_time,spec_desc,receipt_time";
                $data  = $model->field($field)->where($where)->order($order)->select();
                foreach ($data as &$val) {
                    $spec_desc    = json_decode($val['spec_desc'], true);
                    $product_info = null;
                    foreach ($spec_desc as $v) {
                        if ($product_info) {
                            $product_info .= '-' . $v['value'];
                        } else {
                            $product_info .= $v['value'];
                        }
                    }
                    $val['receipt_time']      = date("Y-m-d H:i:s", $val['receipt_time']);
                    $val['product_name']      = $val['product_name'] . '【' . $product_info . '】';
                    $val['total_amount']      = $val['total_amount'] + $val['post_amount'];
                    $val['shop_name']         = $shop_data[$val['shop_id']]['shop_name'];
                    $val['create_time_view']  = date('Y-m-d H:i:s', $val['create_time']);
                    $val['order_status_view'] = $order_status_view[$val['order_status']];
                    if ($val['product_pay_amount'] > 0) {
                        $val['money']               = $val['income'];
                        $val['order_status_remark'] = '<span style="color: #00F7DE">正常</span>';;
                        $money += $val['income'];
                    } else {
                        $val['order_status_remark'] = '<span style="color: red">异常</span>';
                    }
                }
                $this->output($data, sprintf('%.2f', $money * 0.994));
            }
        } else {
            if (IS_AJAX) {
                $this->output(array(), $money);
            }
        }
        $this->assign('shop_data', $shop_data);
        $this->display();
    }

    /**
     * 下载数据
     */
    public function downData() {
        $shop_data         = $this->_getShop();
        $pay_time          = I('get.pay_time', '', 'trim');
        $receipt_time      = I('get.receipt_time', '', 'trim');
        $data_status       = I('get.data_status', '', 'trim');
        $shop_id           = I('get.shop_id', 0, 'int');
        $where             = array('order_status' => array('in', '5,9'));
        $order_status_view = array(5 => '已完成', 9 => '退货失败');
        $order             = 'create_time asc';
        if ($pay_time) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
            $order = 'pay_time asc';
        }
        if ($receipt_time) {
            list($start_time, $end_time) = explode(' ~ ', $receipt_time);
            if ($start_time && $end_time) {
                $start_time            = strtotime($start_time);
                $end_time              = strtotime($end_time) + 86399;
                $where['receipt_time'] = array('between', array($start_time, $end_time));
            }
            $order = 'receipt_time asc';
        }
        if (!empty($data_status)) {
            $where['product_pay_amount'] = 0;
        }
        if ($shop_id > 0) {
            $where['shop_id'] = $shop_id;
        }
        $model = M('order');
        $field = "order_id,shop_id,order_status,product_id,product_name,pay_time,total_amount,commission,product_pay_amount,income,create_time,spec_desc,receipt_time";
        $data  = $model->field($field)->where($where)->order($order)->select();
        foreach ($data as &$val) {
            $spec_desc    = json_decode($val['spec_desc'], true);
            $product_info = null;
            foreach ($spec_desc as $v) {
                if ($product_info) {
                    $product_info .= '-' . $v['value'];
                } else {
                    $product_info .= $v['value'];
                }
            }
            $val['receipt_time']      = date("Y-m-d H:i:s", $val['receipt_time']);
            $val['product_name']      = $val['product_name'] . '【' . $product_info . '】';
            $val['total_amount']      = $val['total_amount'] + $val['post_amount'];
            $val['shop_name']         = $shop_data[$val['shop_id']]['shop_name'];
            $val['create_time_view']  = date('Y-m-d H:i:s', $val['create_time']);
            $val['order_status_view'] = $order_status_view[$val['order_status']];
            if ($val['product_pay_amount'] > 0) {
                $val['money']               = $val['income'];
                $val['order_status_remark'] = '正常';;
            } else {
                $val['order_status_remark'] = '异常';
            }
        }
        $key_name  = array(
            'order_id'            => '订单编号',
            'shop_name'           => '店铺名称',
            'product_name'        => '商品名称',
            'total_amount'        => '支付金额',
            'create_time_view'    => '下单时间',
            'pay_time'            => '付款时间',
            'receipt_time'        => '收货时间',
            'order_status_view'   => '订单状态',
            'order_status_remark' => '数据状态',
            'product_pay_amount'  => '拍单价',
            'commission'          => '达人服务费',
            'money'               => '利润',
        );
        $file_name = '放心购预估利润';
        if ($pay_time) {
            $file_name = "放心购预估-下单时间{$pay_time}-利润";
        }
        if ($receipt_time) {
            $file_name = "放心购预估-收货时间{$receipt_time}-利润";
        }
        download_xls($data, $key_name, $file_name);
    }

    /**
     * 订单利润
     */
    public function settle() {
        $shop_data    = $this->_getShop();
        $pay_time     = I('get.pay_time', '', 'trim');
        $receipt_time = I('get.receipt_time', '', 'trim');
        $data_status  = I('get.data_status', '', 'trim');
        $shop_id      = I('get.shop_id', 0, 'int');
        $where        = array('order_status' => array('in', '5,9'));
        $order        = 'create_time desc';
        if (!empty($data_status)) {
            $where['settle_income'] = 0;
        }
        if ($shop_id > 0) {
            $where['shop_id'] = $shop_id;
        }
        $order_status_view = array(5 => '已完成', 9 => '退货失败');
        $money             = 0;
        if (!empty($pay_time) || !empty($receipt_time)) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
                $order                = 'pay_time asc';
            }
            list($start_time, $end_time) = explode(' ~ ', $receipt_time);
            if ($start_time && $end_time) {
                $start_time            = strtotime($start_time);
                $end_time              = strtotime($end_time) + 86399;
                $where['receipt_time'] = array('between', array($start_time, $end_time));
                $order                 = 'receipt_time asc';
            }

            $model = M('order');
            if (IS_AJAX) {
                $field = "order_id,shop_id,order_status,product_id,product_name,pay_time,total_amount,commission,product_pay_amount,income,settle_amount,settle_income,create_time,spec_desc,receipt_time";
                $data  = $model->field($field)->where($where)->order($order)->select();
                foreach ($data as &$val) {
                    $spec_desc    = json_decode($val['spec_desc'], true);
                    $product_info = null;
                    foreach ($spec_desc as $v) {
                        if ($product_info) {
                            $product_info .= '-' . $v['value'];
                        } else {
                            $product_info .= $v['value'];
                        }
                    }
                    $val['receipt_time']      = date("Y-m-d H:i:s", $val['receipt_time']);
                    $val['product_name']      = $val['product_name'] . '【' . $product_info . '】';
                    $val['shop_name']         = $shop_data[$val['shop_id']]['shop_name'];
                    $val['create_time_view']  = date('Y-m-d H:i:s', $val['create_time']);
                    $val['order_status_view'] = $order_status_view[$val['order_status']];
                    if ($val['settle_income'] > 0) {
                        $val['money']               = $val['settle_income'];
                        $val['order_status_remark'] = '<span style="color: #00F7DE">正常</span>';;
                        $money += $val['settle_income'];
                    } else {
                        $val['money']               = $val['settle_income'];
                        $val['order_status_remark'] = '<span style="color: red">异常</span>';
                    }
                }
                $this->output($data, $money);
            }
        } else {
            if (IS_AJAX) {
                $this->output(array(), $money);
            }
        }
        $this->assign('shop_data', $shop_data);
        $this->display();
    }

    /**
     * 下载数据
     */
    public function settleDownData() {
        $shop_data         = $this->_getShop();
        $pay_time          = I('get.pay_time', '', 'trim');
        $receipt_time      = I('get.receipt_time', '', 'trim');
        $data_status       = I('get.data_status', '', 'trim');
        $shop_id           = I('get.shop_id', 0, 'int');
        $where             = array('order_status' => array('in', '5,9'));
        $order_status_view = array(5 => '已完成', 9 => '退货失败');
        $order             = 'create_time asc';
        if ($pay_time) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
            $order = 'pay_time asc';
        }
        if ($receipt_time) {
            list($start_time, $end_time) = explode(' ~ ', $receipt_time);
            if ($start_time && $end_time) {
                $start_time            = strtotime($start_time);
                $end_time              = strtotime($end_time) + 86399;
                $where['receipt_time'] = array('between', array($start_time, $end_time));
            }
            $order = 'receipt_time asc';
        }
        if (!empty($data_status)) {
            $where['settle_income'] = 0;
        }
        if ($shop_id > 0) {
            $where['shop_id'] = $shop_id;
        }
        $model = M('order');
        $field = "order_id,shop_id,order_status,product_id,product_name,pay_time,total_amount,commission,product_pay_amount,income,create_time,settle_income,spec_desc,receipt_time";
        $data  = $model->field($field)->where($where)->order($order)->select();
        foreach ($data as &$val) {
            $spec_desc    = json_decode($val['spec_desc'], true);
            $product_info = null;
            foreach ($spec_desc as $v) {
                if ($product_info) {
                    $product_info .= '-' . $v['value'];
                } else {
                    $product_info .= $v['value'];
                }
            }
            $val['receipt_time']      = date("Y-m-d H:i:s", $val['receipt_time']);
            $val['product_name']      = $val['product_name'] . '【' . $product_info . '】';
            $val['shop_name']         = $shop_data[$val['shop_id']]['shop_name'];
            $val['create_time_view']  = date('Y-m-d H:i:s', $val['create_time']);
            $val['order_status_view'] = $order_status_view[$val['order_status']];
            if ($val['settle_income'] > 0) {
                $val['money']               = $val['settle_income'];
                $val['order_status_remark'] = '正常';;
            } else {
                $val['money']               = $val['settle_income'];
                $val['order_status_remark'] = '异常';
            }
        }
        $key_name  = array(
            'order_id'            => '订单编号',
            'shop_name'           => '店铺名称',
            'product_name'        => '商品名称',
            'create_time_view'    => '下单时间',
            'pay_time'            => '付款时间',
            'receipt_time'        => '收货时间',
            'order_status_view'   => '订单状态',
            'order_status_remark' => '数据状态',
            'total_amount'        => '支付金额',
            'settle_amount'       => '结算金额',
            'commission'          => '达人服务费',
            'product_pay_amount'  => '拍单价',
            'money'               => '利润',
        );
        $file_name = '放心购结算利润';
        if ($pay_time) {
            $file_name = "放心购结算-下单时间{$pay_time}-利润";
        }
        if ($receipt_time) {
            $file_name = "放心购结算-收货时间{$receipt_time}-利润";
        }
        download_xls($data, $key_name, $file_name);
    }

    /**
     * 设置订单结算价
     */
    public function setSettleAmount() {
        set_time_limit(300);
        $filename = $_FILES['filename'];
        if (!$filename) {
            $this->error('未发现文件！');
        }
        list($name, $ext) = explode('.', $filename['name']);
        $file_path = ROOT_PATH . "/Uploads/order-settle" . date("Y-m-d") . '.' . $ext;
        @move_uploaded_file($filename['tmp_name'], $file_path);
        if (file_exists($file_path)) {
            $data   = array();
            $handle = fopen($file_path, "r");
            while ($temp = fgetcsv($handle, 1000, ",")) {
                $data[] = $temp;
            }
            unset($data[0]);
            $order_data = array();
            foreach ($data as $val) {
                $order_id = str_replace(array("'", "\r", "\n", "\r\n"), '', $val[1]);
                if (isset($order_data[$order_id])) {
                    $order_data[$order_id] = sprintf('%.2f', $order_data[$order_id] + $val[6]);
                } else {
                    $order_data[$order_id] = $val[6];
                }
            }
            $db_order = M('order')->where(array('order_id' => array('in', array_keys($order_data)), 'settle_amount' => 0, 'order_status' => array('in', '5,9')))->select();
            if (count($db_order) > 0) {
                foreach ($db_order as $order) {
                    M('order')->where(array('id' => $order['id']))->save(array('settle_amount' => $order_data[$order['order_id']], 'settle_income' => $order_data[$order['order_id']] - $order['product_pay_amount']));
                }
                $this->success('处理成功');
            } else {
                $this->error('所有订单均已处理，请重新上传未处理的订单！');
            }
        } else {
            $this->error('上传失败！');
        }
    }
}