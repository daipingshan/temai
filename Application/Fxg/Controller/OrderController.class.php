<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/15
 * Time: 13:46
 */

namespace Fxg\Controller;

/**
 * Class OrderController
 *
 * @package HaiTao\Controller
 */
class OrderController extends CommonController {

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

    protected $logistics_list = array(
        7  => '圆通快递',
        8  => '申通快递',
        9  => '韵达快递',
        10 => '佳怡物流',
        11 => '优速物流',
        12 => '顺丰快递',
        13 => '德邦物流',
        14 => '天天快递',
        15 => '中通速递',
        16 => '全峰快递',
        17 => 'EMS',
        18 => '微特派',
        19 => '邮政国内小包',
        20 => '百世汇通',
        21 => '宅急送',
        22 => '如风达',
        23 => '增益速递',
        24 => '电子券',
        25 => '国通快递',
        26 => '快捷快递',
        27 => 'E速宝',
        28 => '云购商品',
        30 => '京东快递',
        31 => '万象物流',
        32 => '安能物流',
        33 => '加运美物流',
        34 => '联邦快递',
        35 => '远成快递',
        36 => '信丰物流',
        37 => '黄马甲',
    );
    protected $pay_type_list  = array(
        0 => '货到付款',
        1 => '微信支付',
        2 => '支付宝'
    );

    protected $order_status_list = array(
        1  => '待确认',
        2  => '备货中',
        3  => '已发货',
        4  => '已取消',
        5  => '已完成',
        6  => '退货中-用户申请',
        7  => '退货中-商家同意退货',
        8  => '退货中-客服仲裁',
        9  => '已关闭-退货失败',
        10 => '退货中-客服同意',
        11 => '退货中-用户填写完物流',
        12 => '已关闭-商户同意',
        13 => '退货中-再次客服仲裁',
        14 => '已关闭-客服同意',
        15 => '取消退货申请',
        16 => '申请退款中',
        17 => '商户同意退款',
        18 => '订单退款仲裁中',
        19 => '退款仲裁支持用户',
        20 => '退款仲裁支持商家',
        21 => '订单退款成功',
        22 => '售后退款成功',
        23 => '退货中-再次用户申请',
        24 => '已关闭-退货成功',
    );


    /**
     * 订单列表
     */
    public function index() {
        $logistics_cache = $this->_getLogisticsCache();
        if (IS_AJAX) {
            $need_order_status = array(2, 3, 5, 9);
            $shop_id           = I('get.shop_id', 0, 'int');
            $order_id          = I('get.order_id', '', 'trim');
            $product_keyword   = I('get.product_keyword', '', 'trim');
            $order_source      = I('get.order_source', 0, 'int');
            $post_data         = I('get.post_data', '', 'trim');
            $logistics_id      = I('get.logistics_id', 0, 'int');
            $logistics_code    = I('get.logistics_code', '', 'trim');
            $pay_type          = I('get.pay_type', '', 'trim');
            $pay_time          = I('get.pay_time', '', 'trim');
            $order_status      = I('get.order_status', 0, 'int');
            $data_status       = I('get.data_status', 0, 'int');
            $page              = I('get.page', 1, 'int');
            $limit             = I('get.limit', 10, 'int');
            $where             = array();
            if (!empty($shop_id)) {
                $where['shop_id'] = $shop_id;
            }
            if (!empty($order_status)) {
                if ($order_status == 6) {
                    $where['order_status'] = array('in', '6,7,8,9,10,11,12,13,14,15,22,23,24');
                } else if ($order_status == 7) {
                    $where['order_status'] = array('in', '16,17,18,19,20,21');
                } else {
                    $where['order_status'] = $order_status;
                }
            }
            if (!empty($order_id)) {
                $where['order_id|ali_order_id'] = $order_id;
            }
            if (!empty($product_keyword)) {
                $where['product_id|product_name'] = array('like', "%{$product_keyword}%");
            }
            if (!empty($order_source)) {
                $where['c_type'] = $order_source;
            }
            if (!empty($post_data)) {
                $where['post_receiver|post_tel'] = $post_data;
            }
            if (!empty($logistics_id)) {
                $where['logistics_id'] = $logistics_id;
            }
            if (!empty($logistics_code)) {
                $where['logistics_code'] = $logistics_code;
            }
            if ($pay_type !== '') {
                $where['pay_type'] = $pay_type;
            }
            if (!empty($pay_time)) {
                list($start_time, $end_time) = explode(' ~ ', $pay_time);
                if ($start_time && $end_time) {
                    $start_time           = strtotime($start_time);
                    $end_time             = strtotime($end_time) + 86399;
                    $where['create_time'] = array('between', array($start_time, $end_time));
                }
            }
            if (!empty($data_status)) {
                $where['product_pay_amount'] = 0;
            }
            $shop_data_view = $this->_getShop();
            $data           = array();
            $count          = 0;
            if ($this->user_info['shop_ids']) {
                if (empty($shop_id)) {
                    $where['shop_id'] = array('in', $this->user_info['shop_ids']);
                }
                $model = M('order');
                $count = $model->where($where)->count('id');
                $data  = $model->where($where)->page($page)->limit($limit)->order('create_time desc')->select();
                foreach ($data as &$val) {
                    if (in_array($val['order_status'], $need_order_status)) {
                        $val['is_set'] = 1;
                    } else {
                        $val['is_set'] = 0;
                    }
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
                    $val['logistics_name']    = $logistics_cache[$val['logistics_id']]['name'];
                    $val['product_name']      = $val['product_name'] . '【' . $product_info . '】';
                    $post_addr                = json_decode($val['post_addr'], true);
                    $val['order_source']      = $this->order_source_list[$val['order_source']];
                    $val['post_address']      = $post_addr['province']['name'] . $post_addr['city']['name'] . $post_addr['town']['name'] . $post_addr['detail'];
                    $val['create_time']       = date('Y-m-d H:i:s', $val['create_time']);
                    $val['shop_name']         = $shop_data_view[$val['shop_id']]['shop_name'];
                    $val['order_status_view'] = $this->order_status_list[$val['order_status']];
                    $val['total_amount']      = $val['total_amount'] + $val['post_amount'];
                }
            }

            $this->output($data, $count);
        } else {
            $shop_data = array();
            if ($this->user_info['shop_ids']) {
                $shop_data = M('shop')->where(array('id' => array('in', $this->user_info['shop_ids'])))->select();
            }
            $this->assign('order_source', $this->order_source_list);
            $this->assign('logistics', $logistics_cache);
            $this->assign('pay_type', $this->pay_type_list);
            $this->assign('shop_data', $shop_data);
            $this->display();
        }
    }

    /**
     * 下载订单
     */
    public function downData() {
        $shop_id         = I('get.shop_id', 0, 'int');
        $order_id        = I('get.order_id', '', 'trim');
        $product_keyword = I('get.product_keyword', '', 'trim');
        $order_source    = I('get.order_source', 0, 'int');
        $post_data       = I('get.post_data', '', 'trim');
        $logistics_id    = I('get.logistics_id', 0, 'int');
        $logistics_code  = I('get.logistics_code', '', 'trim');
        $pay_type        = I('get.pay_type', '', 'trim');
        $pay_time        = I('get.pay_time', '', 'trim');
        $order_status    = I('get.order_status', 0, 'int');
        $data_status     = I('get.data_status', '', 'trim');
        $where           = array();
        if (!empty($shop_id)) {
            $where['shop_id'] = $shop_id;
        }
        if (!empty($order_status)) {
            if ($order_status == 6) {
                $where['order_status'] = array('in', '6,7,8,9,10,11,12,13,14,15,22,23,24');
            } else if ($order_status == 7) {
                $where['order_status'] = array('in', '16,17,18,19,20,21');
            } else {
                $where['order_status'] = $order_status;
            }
        }
        if (!empty($order_id)) {
            $where['order_id'] = $order_id;
        }
        if (!empty($product_keyword)) {
            $where['product_id|product_name'] = array('like', "%{$product_keyword}%");
        }
        if (!empty($order_source)) {
            $where['c_type'] = $order_source;
        }
        if (!empty($post_data)) {
            $where['post_receiver|post_tel'] = $post_data;
        }
        if (!empty($logistics_id)) {
            $where['logistics_id'] = $logistics_id;
        }
        if (!empty($logistics_code)) {
            $where['logistics_code'] = $logistics_code;
        }
        if ($pay_type !== '') {
            $where['pay_type'] = $pay_type;
        }
        if (!empty($pay_time)) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
        }
        if (!empty($data_status)) {
            $where['product_pay_amount'] = 0;
        }
        $shop_data_view = $this->_getShop();
        $data           = array();
        if ($this->user_info['shop_ids']) {
            if (empty($shop_id)) {
                $where['shop_id'] = array('in', $this->user_info['shop_ids']);
            }
            $model = M('order');
            $data  = $model->where($where)->order('create_time desc')->select();
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
                $post_addr                = json_decode($val['post_addr'], true);
                $val['order_source']      = $this->order_source_list[$val['order_source']];
                $val['post_address']      = $post_addr['province']['name'] . $post_addr['city']['name'] . $post_addr['town']['name'] . $post_addr['detail'];
                $val['create_time']       = date('Y-m-d H:i:s', $val['create_time']);
                $val['shop_name']         = $shop_data_view[$val['shop_id']]['shop_name'];
                $val['order_status_view'] = $this->order_status_list[$val['order_status']];
                $val['total_money']       = $val['total_amount'] + $val['post_amount'];
            }
        }
        $key_name  = array(
            'order_id'           => '订单编号',
            'shop_name'          => '店铺名称',
            'product_name'       => '商品名称',
            'combo_num'          => '购买数量',
            'total_money'        => '总付款金额',
            'total_amount'       => '付款金额',
            'post_amount'        => '运费',
            'product_pay_amount' => '拍单金额',
            'post_receiver'      => '收件人',
            'post_tel'           => '收件电话',
            'post_address'       => '收件地址',
            'order_status_view'  => '订单状态',
            'create_time'        => '下单时间',
            'receipt_time'       => '收货时间',
            'pay_time'           => '付款时间',
            'buyer_words'        => '用户留言',
            'ali_order_id'       => '阿里订单编号',
        );
        $file_name = '放心购订单-下载时间' . date('Y-m-d');
        if ($pay_time) {
            $file_name = "放心购订单-下单时间{$pay_time}";
        }
        download_xls($data, $key_name, $file_name);
    }

    /**
     * 设置商品拍单价
     */
    public function setProductAmount() {
        set_time_limit(300);
        $filename = $_FILES['filename'];
        if (!$filename) {
            $this->error('未发现文件！');
        }
        list($name, $ext) = explode('.', $filename['name']);
        $file_path = ROOT_PATH . "/Uploads/order-" . date("Y-m-d") . '.' . $ext;
        @move_uploaded_file($filename['tmp_name'], $file_path);
        if (file_exists($file_path)) {
            require_once(APP_PATH . "/Common/Org/PHPExcel.class.php");
            require_once(APP_PATH . "/Common/Org/PHPExcel/IOFactory.php");
            $reader   = \PHPExcel_IOFactory::createReader('Excel2007');
            $PHPExcel = $reader->load($file_path); // 载入excel文件
            $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
            $data     = $obj->toArray();
            unset($data[0]);
            $order_data = array();
            $error_msg  = null;
            foreach ($data as $key => $val) {
                $order_id     = str_replace(array("'", "\r", "\n", "\r\n"), '', $val[0]);
                $ali_order_id = str_replace(array("'", "\r", "\n", "\r\n"), '', $val[1]);
                $amount       = $val[2];
                if (empty($order_id)) {
                    $error_msg = "第{$key}条数据，订单号错误！";
                    break;
                }
                if (empty($ali_order_id)) {
                    $error_msg = "第{$key}条数据，【{$order_id}】,阿里订单号错误！";
                    break;
                }
                if ($amount <= 0) {
                    $error_msg = "第{$key}条数据，【{$order_id}】,拍单价错误！";
                    break;
                }
                $order_data[$order_id] = array('ali_order_id' => $val[1], 'amount' => $val[2]);
            }
            if ($error_msg) {
                $this->error($error_msg);
            }
            $db_order = M('order')->where(array('order_id' => array('in', array_keys($order_data)), 'product_pay_amount' => 0))->select();
            if (count($db_order) > 0) {
                foreach ($db_order as $order) {
                    $save_data = array('ali_order_id' => $order_data[$order['order_id']]['ali_order_id'], 'income' => $order['total_amount'] + $order['post_amount'] - $order['commission'] - $order_data[$order['order_id']]['amount'], 'product_pay_amount' => $order_data[$order['order_id']]['amount']);
                    M('order')->where(array('id' => $order['id']))->save($save_data);
                }
                $this->success('处理成功');
            } else {
                $this->error('所有订单均已处理，请重新上传未处理的订单！');
            }
        } else {
            $this->error('上传失败！');
        }
    }

    /**
     * 设置商品拍单价
     */
    public function setOrderStatus() {
        set_time_limit(300);
        $filename = $_FILES['filename'];
        if (!$filename) {
            $this->error('未发现文件！');
        }
        list($name, $ext) = explode('.', $filename['name']);
        $file_path = ROOT_PATH . "/Uploads/order-send-" . date("Y-m-d") . '.' . $ext;
        @move_uploaded_file($filename['tmp_name'], $file_path);
        if (file_exists($file_path)) {
            require_once(APP_PATH . "/Common/Org/PHPExcel.class.php");
            require_once(APP_PATH . "/Common/Org/PHPExcel/IOFactory.php");
            $reader   = \PHPExcel_IOFactory::createReader('Excel2007');
            $PHPExcel = $reader->load($file_path); // 载入excel文件
            $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
            $data     = $obj->toArray();
            unset($data[0]);
            $logistics_data = $this->_getLogisticsList();
            $ali_order_data = array();
            $error_msg      = $success_msg = $msg = null;
            $error_num      = 0;
            foreach ($data as $key => $val) {
                $order_id = str_replace(array("'", "\r", "\n", "\r\n"), '', $val[0]);
                if (!isset($logistics_data[$val[1]])) {
                    $error_msg = "第{$key}条数据，【{$order_id}】,快递代码错误！";
                    break;
                }
                if (empty($val[2])) {
                    $error_msg = "第{$key}条数据，【{$order_id}】,快递单号错误！";
                    break;
                }
                $ali_order_data[$order_id] = array(
                    'order_id'       => $order_id,
                    'logistics_id'   => $logistics_data[$val[1]]['logistics_id'],
                    'logistics_name' => $logistics_data[$val[1]]['name'],
                    'logistics_code' => $val[2],
                );
            }
            if ($error_msg) {
                $this->error($error_msg);
            }
            $db_order = M('order')->where(array('ali_order_id' => array('in', array_keys($ali_order_data)), 'order_status' => 2))->select();
            if (count($db_order) > 0) {
                $method = "order.logisticsAdd";
                foreach ($db_order as $order) {
                    $param = array('order_id' => $order['pid'], 'logistics_id' => $ali_order_data[$order['ali_order_id']]['logistics_id'], 'logistics_code' => $ali_order_data[$order['ali_order_id']]['logistics_code']);
                    $res   = $this->_getOpenData($order['shop_id'], $param, $method);
                    $msg   .= "【{$order['order_id']}】,{$res['info']}\r\n";
                    if ($res['status'] == 0) {
                        $error_num++;
                    } else {
                        M('order')->where(array('id' => $order['id']))->save(array('order_status' => 3));
                    }
                }
                $count_num   = count($db_order);
                $success_num = $count_num - $error_num;
                $success_msg = "发货成功\r\n总发货【{$count_num}】个订单，成功【{$success_num}】个；失败【{$error_num}】个。\r\n{$msg}";
                M('order_error')->add(array('order_id' => date('YmdHis'), 'error_info' => $success_msg, 'create_time' => date('Y-m-d H:i:s'), 'type' => 1));
                $this->success($success_msg);
            } else {
                $this->error('所有订单均已处理，请重新上传未处理的订单！');
            }
        } else {
            $this->error('上传失败！');
        }
    }

    /**
     * 设置费用
     */
    public function setOrderField() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $order_id = I('post.order_id', 0, 'int');
        $info     = M('order')->find($order_id);
        if (empty($order_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $order_field       = I('post.order_field', '', 'trim');
        $order_field_value = I('post.order_field_value', '', 'trim');
        if (empty($order_field)) {
            $this->error('请求参数不合法！');
        }
        if ($order_field == 'product_pay_amount') {
            if ($order_field_value <= 0) {
                $this->error('商品拍单价必须大于0');
            }
            $save_data = array('product_pay_amount' => $order_field_value, 'income' => $info['total_amount'] + $info['post_amount'] - $info['commission'] - $order_field_value);
        } else {
            if (empty($order_field_value)) {
                $this->error('阿里订单号不能为空！');
            }
            $save_data = array('ali_order_id' => $order_field_value);
        }
        $res = M('order')->where(array('id' => $order_id))->save($save_data);
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 订单确认
     */
    public function orderConfirm() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $order_id = I('post.order_id', 0, 'int');
        $info     = M('order')->find($order_id);
        if (empty($order_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        if ($info['pay_type'] != 0) {
            $this->error('该订单不是货到付款订单无需订单确认！');
        }
        if ($info['order_status'] != 1) {
            $this->error('订单状态不符合确认要求，无法确认订单！');
        }
        $method = "order.stockUp";
        $param  = array('order_id' => $info['pid']);
        $res    = $this->_getOpenData($info['shop_id'], $param, $method);
        if ($res['status'] == 0) {
            M('order_error')->add(array('order_id' => $info['order_id'], 'error_info' => $res['info'], 'create_time' => date('Y-m-d H:i:s'), 'type' => 2));
            $this->error($res['info']);
        } else {
            M('order')->where(array('id' => $info['id']))->save(array('order_status' => 2));
            $this->success('确认成功');
        }
    }

    /**
     * 订单重新发货
     */
    public function setLogistics() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $order_id               = I('post.order_id', 0, 'int');
        $logistics_english_name = I('post.logistics_english_name', '', 'trim');
        $logistics_code         = I('post.logistics_code', '', 'trim');
        $order                  = M('order')->find($order_id);
        if (empty($order_id) || empty($order)) {
            $this->error('请求参数不合法！');
        }
        if ($order['order_status'] != 3) {
            $this->error('订单状态不符合确认要求，无法重新发货！');
        }
        if (empty($logistics_english_name)) {
            $this->error('快递代码不能为空！');
        }
        $logistics_data = $this->_getLogisticsList();
        if (!isset($logistics_data[$logistics_english_name])) {
            $this->error('快递代码不存在，请前往快递管理中添加快递代码后在进行发货！');
        }
        if (empty($logistics_code)) {
            $this->error('快递单号不能为空！');
        }
        $method = "order.logisticsEdit";
        $param  = array('order_id' => $order['pid'], 'logistics_id' => $logistics_data[$logistics_english_name]['logistics_id'], 'logistics_code' => $logistics_code);
        $res    = $this->_getOpenData($order['shop_id'], $param, $method);
        if ($res['status'] == 0) {
            M('order_error')->add(array('order_id' => $order['order_id'], 'error_info' => $res['info'], 'create_time' => date('Y-m-d H:i:s'), 'type' => 3));
            $this->error($res['info']);
        } else {
            M('order')->where(array('id' => $order['id']))->save(array('logistics_id' => $logistics_data[$logistics_english_name]['logistics_id'], 'logistics_code' => $logistics_code));
            $this->success('发货成功');
        }
    }
}