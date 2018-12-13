<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/15
 * Time: 13:46
 */

namespace HaiTao\Controller;

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
    protected $logistics_list    = array(
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
    protected $pay_type_list     = array(
        0 => '货到付款',
        1 => '在线支付'
    );

    /**
     * 订单列表
     */
    public function index() {


        if (IS_AJAX) {
            $order_id        = I('get.order_id', '', 'trim');
            $product_keyword = I('get.product_keyword', '', 'trim');
            $order_source    = I('get.order_source', 0, 'int');
            $post_receiver   = I('get.post_receiver', '', 'trim');
            $post_tel        = I('get.post_tel', '', 'trim');
            $logistics_id    = I('get.logistics_id', 0, 'int');
            $logistics_code  = I('get.logistics_code', '', 'trim');
            $pay_type        = I('get.pay_type', 0, 'int');
            $pay_time        = I('get.pay_time', '', 'trim');
            $finish_time     = I('get.finish_time', '', 'trim');
            $order_status    = I('get.order_status', 0, 'int');
            $urge_cnt        = I('get.urge_cnt', '', 'trim');
            $page            = I('get.page', 1, 'int');
            $limit           = I('get.limit', 10, 'int');
            $where           = array();
            if (!empty($order_status)) {
                if ($order_status == 99) {
                    $where['order_status'] = 2;
                    $where['is_buy']       = 0;
                } else if ($order_status == 999) {
                    $where['order_status'] = 2;
                    $where['is_buy']       = 1;
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
                $where['order_source'] = $order_source;
            }
            if (!empty($post_receiver)) {
                $where['post_receiver'] = $post_receiver;
            }
            if (!empty($post_tel)) {
                $where['post_tel'] = $post_tel;
            }
            if (!empty($logistics_id)) {
                $where['logistics_id'] = $logistics_id;
            }
            if (!empty($logistics_code)) {
                $where['logistics_code'] = $logistics_code;
            }
            if (!empty($pay_type)) {
                $where['pay_type'] = $pay_type;
            }
            if (!empty($pay_time)) {
                list($start_time, $end_time) = explode(' ~ ', $pay_time);
                if ($start_time && $end_time) {
                    $start_time        = strtotime($start_time);
                    $end_time          = strtotime($end_time);
                    $where['pay_time'] = array('between', array($start_time, $end_time));
                }
            }
            if (!empty($finish_time)) {
                list($start_time, $end_time) = explode(' ~ ', $finish_time);
                if ($start_time && $end_time) {
                    $start_time           = strtotime($start_time);
                    $end_time             = strtotime($end_time);
                    $where['finish_time'] = array('between', array($start_time, $end_time));
                }
            }
            if (!empty($urge_cnt)) {
                $where['urge_cnt'] = array('gt', 0);
            }
            $model = M('order');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('pay_time desc')->select();
            foreach ($data as &$val) {
                $post_addr               = json_decode($val['post_addr'], true);
                $val['order_source']     = $this->order_source_list[$val['order_source']];
                $val['post_address']     = $post_addr['province']['name'] . $post_addr['city']['name'] . $post_addr['town']['name'] . $post_addr['detail'];
                $val['pay_type']         = $this->pay_type_list[$val['pay_type']];
                $val['pay_time']         = date('Y-m-d H:i', $val['pay_time']);
                $val['finish_time']      = $val['finish_time'] > 0 ? date('Y-m-d H:i', $val['finish_time']) : '';
                $val['logistics_detail'] = json_decode($val['logistics_detail'], true);
                $val['logistics_name']   = $this->logistics_list[$val['logistics_id']] . "-" . $val['logistics_code'];
                switch ($val['order_status']) {
                    case 1 :
                        $val['order_status_view'] = "待确认";
                        break;
                    case 2 :
                        if ($val['is_buy'] == 0) {
                            $val['order_status_view'] = "待拍单";
                        } else {
                            $val['order_status_view'] = "待发货";
                        }
                        break;
                    case 3 :
                        $val['order_status_view'] = "已发货";
                        break;
                    case 4 :
                        $val['order_status_view'] = "已取消";
                        break;
                    case 5 :
                        $val['order_status_view'] = "已完成";
                        break;
                    case 6 :
                        $val['order_status_view'] = "退货中";
                        break;
                    case  7:
                        $val['order_status_view'] = "退货成功";
                        break;
                    case 8 :
                        $val['order_status_view'] = "退货失败";
                        break;
                    case 9 :
                        $val['order_status_view'] = "异常订单";
                        break;
                    default:
                        $val['order_status_view'] = "未知";
                        break;
                }
            }
            $this->output($data, $count);
        } else {
            $this->assign('order_source', $this->order_source_list);
            $this->assign('logistics', $this->logistics_list);
            $this->assign('pay_type', $this->pay_type_list);
            $this->display();
        }
    }

    /**
     * 获取供货商列表
     */
    public function getPartnerList() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $product_id = I('get.product_id', '', 'trim');
        $data       = M('item_partner')->alias('i_p')->field('i_p.*,p.name,p.mobile')->join('left join ht_partner p ON p.id = i_p.partner_id ')->where(array('i_p.item_id' => $product_id, 'i_p.status' => 1))->select();
        if (empty($product_id) || empty($data)) {
            $this->error('商品尚未绑定供货商无法购买!');
        }
        $this->success($data);
    }

    /**
     * 获取供货商列表
     */
    public function getCourierList() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_partner_id = I('get.item_partner_id', '', 'trim');
        $info            = M('item_partner')->find($item_partner_id);
        if (empty($item_partner_id) || empty($info)) {
            $this->error('请求参数不合法!');
        }
        $data = M('courier_partner')->alias('c_p')->field('c_p.id,c.username,c.mobile,c.alipay_account')->join('left join ht_courier c ON c.id = c_p.courier_id ')->where(array('c_p.partner_id' => $info['partner_id']))->select();
        if (empty($data)) {
            $this->error('供货商尚未绑定快递员!');
        }
        $this->success($data);
    }

    /**
     * 设置订单关联供货商信息
     */
    public function setOrderItemPartnerId() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_partner_id = I('get.item_partner_id', 0, 'int');
        $order_id        = I('get.order_id', 0, 'int');
        $info            = M('item_partner')->find($item_partner_id);
        $content         = json_decode($info['content'], true);
        $order_info      = M('order')->find($order_id);
        $spec_desc       = json_decode($order_info['spec_desc']);
        if (empty($item_partner_id) || empty($order_id) || empty($info) || empty($order_info)) {
            $this->error('请求参数不合法!');
        }
        if (count($spec_desc) == 2) {
            $name = $spec_desc[0]['value'] . '@' . $spec_desc[1]['value'];
        } else {
            $name = $spec_desc[0]['value'];
        }
        if (empty($name)) {
            $this->error('该订单未找到规格，请联系管理员');
        }
        $price = 0;
        foreach ($content as $val) {
            if ($val['name'] == $name) {
                $price = $val['price'];
            }
        }
        if (empty($price)) {
            $this->error('该供货商下没有此规格商品，请添加后在购买！');
        }
        $courier_data = M('courier_partner')->where(array('partner_id' => $info['partner_id']))->select();
        $save_data    = array('is_buy' => 1, 'item_partner_id' => $item_partner_id, 'supple_price' => $price);
        if (count($courier_data) == 1) {
            $save_data['courier_partner_id'] = $courier_data[0]['id'];
        }
        $res = M('order')->where(array('id' => $order_id))->save($save_data);
        if ($res) {
            $this->success('关联供货商成功');
        } else {
            $this->error('关联供货商失败');
        }
    }

    /**
     * 设置订单关联快递员信息
     */
    public function setOrderCourierPartnerId() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $courier_partner_id = I('get.courier_partner_id', 0, 'int');
        $order_id           = I('get.order_id', 0, 'int');
        $info               = M('courier_partner')->find($courier_partner_id);
        if (empty($courier_partner_id) || empty($order_id) || empty($info)) {
            $this->error('请求参数不合法!');
        }
        $save_data = array('courier_partner_id' => $courier_partner_id);
        $res       = M('order')->where(array('id' => $order_id))->save($save_data);
        if ($res) {
            $this->success('关联快递员成功');
        } else {
            $this->error('关联快递员失败');
        }
    }

    /**
     * 查看订单关联供货商信息
     */
    public function checkOrderPartner() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_partner_id = I('get.id', 0, 'int');
        $info            = M('item_partner')->alias('i_p')->field('i_p.*,p.name,p.mobile')->join('left join ht_partner p ON p.id = i_p.partner_id ')->where(array('i_p.id' => $item_partner_id))->find();
        if (empty($item_partner_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $tip = "供货商名称：{$info['name']}，供货商电话：{$info['mobile']}，供货价格：{$info['price']}";
        $this->success($tip);
    }

    /**
     * 查看订单关联快递员信息
     */
    public function checkOrderCourier() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $courier_partner_id = I('get.id', 0, 'int');
        $info               = M('courier_partner')->alias('c_p')->field('c_p.id,c.username,c.mobile,c.alipay_account')->join('left join ht_courier c ON c.id = c_p.courier_id ')->where(array('c_p.id' => $courier_partner_id))->find();
        if (empty($courier_partner_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $tip = "真实姓名：{$info['username']}，支付宝账号：{$info['alipay_account']}，手机号码：{$info['mobile']}";
        $this->success($tip);
    }

    /**
     * 设置操作备注
     */
    public function setRemark() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $order_id    = I('post.order_id', 0, 'int');
        $remark      = I('post.remark', '', 'trim');
        $fail_status = I('post.fail_status', 0, 'int');
        if (empty($remark)) {
            $this->error('操作备注不能为空！');
        }
        $info = M('order')->find($order_id);
        if (empty($order_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $remark    = "操作人：" . session('ht_user_info')['username'] . "，时间：" . date('Y-m-d H:i') . "，" . $remark;
        $remark    = $info['remark'] ? $info['remark'] . "<br>" . $remark : $remark;
        $save_data = array('remark' => $remark);
        if ($fail_status) {
            $save_data['fail_status'] = $fail_status;
        }
        $res = M('order')->where(array('id' => $order_id))->save($save_data);
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * 设置费用
     */
    public function setFree() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $order_id = I('post.order_id', 0, 'int');
        $info     = M('order')->find($order_id);
        if (empty($order_id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $supple_price        = I('post.supple_price', '', 'trim');
        $commission          = I('post.commission', '', 'trim');
        $out_logistics_money = I('post.out_logistics_money', '', 'trim');
        $get_logistics_money = I('post.get_logistics_money', '', 'trim');
        if (empty($out_logistics_money) || $out_logistics_money == 0) {
            $this->error('寄件运费必须大于0！');
        }
        if (empty($supple_price) || $supple_price == 0) {
            $this->error('供货价必须大于0！');
        }
        $res = M('order')->where(array('id' => $order_id))->save(array('supple_price' => $supple_price, 'out_logistics_money' => $out_logistics_money, 'get_logistics_money' => $get_logistics_money, 'commission' => $commission));
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 下载订单数据
     */
    public function downData() {
        $order_id        = I('get.order_id', '', 'trim');
        $product_keyword = I('get.product_keyword', '', 'trim');
        $order_source    = I('get.order_source', 0, 'int');
        $post_receiver   = I('get.post_receiver', '', 'trim');
        $post_tel        = I('get.post_tel', '', 'trim');
        $logistics_id    = I('get.logistics_id', 0, 'int');
        $logistics_code  = I('get.logistics_code', '', 'trim');
        $pay_type        = I('get.pay_type', 0, 'int');
        $pay_time        = I('get.pay_time', '', 'trim');
        $finish_time     = I('get.finish_time', '', 'trim');
        $order_status    = I('get.order_status', 0, 'int');
        $urge_cnt        = I('get.urge_cnt', '', 'trim');
        $where           = array();
        if (!empty($order_status)) {
            if ($order_status == 99) {
                $where['order_status'] = 2;
                $where['is_buy']       = 0;
            } else if ($order_status == 999) {
                $where['order_status'] = 2;
                $where['is_buy']       = 1;
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
            $where['order_source'] = $order_source;
        }
        if (!empty($post_receiver)) {
            $where['post_receiver'] = $post_receiver;
        }
        if (!empty($post_tel)) {
            $where['post_tel'] = $post_tel;
        }
        if (!empty($logistics_id)) {
            $where['logistics_id'] = $logistics_id;
        }
        if (!empty($logistics_code)) {
            $where['logistics_code'] = $logistics_code;
        }
        if (!empty($pay_type)) {
            $where['pay_type'] = $pay_type;
        }
        if (!empty($pay_time)) {
            list($start_time, $end_time) = explode(' ~ ', $pay_time);
            if ($start_time && $end_time) {
                $start_time        = strtotime($start_time);
                $end_time          = strtotime($end_time);
                $where['pay_time'] = array('between', array($start_time, $end_time));
            }
        }
        if (!empty($finish_time)) {
            list($start_time, $end_time) = explode(' ~ ', $finish_time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time);
                $where['finish_time'] = array('between', array($start_time, $end_time));
            }
        }
        if (!empty($urge_cnt)) {
            $where['urge_cnt'] = array('gt', 0);
        }
        $model = M('order');
        $data  = $model->where($where)->order('pay_time desc')->select();
        foreach ($data as &$val) {
            $post_addr               = json_decode($val['post_addr'], true);
            $val['order_source']     = $this->order_source_list[$val['order_source']];
            $val['post_address']     = $post_addr['province']['name'] . $post_addr['city']['name'] . $post_addr['town']['name'] . $post_addr['detail'];
            $val['pay_type']         = $this->pay_type_list[$val['pay_type']];
            $val['pay_time']         = date('Y-m-d H:i', $val['pay_time']);
            $val['finish_time']      = $val['finish_time'] > 0 ? date('Y-m-d H:i', $val['finish_time']) : '';
            $val['logistics_detail'] = json_decode($val['logistics_detail'], true);
            $val['logistics_name']   = $this->logistics_list[$val['logistics_id']] . "-" . $val['logistics_code'];
            if ($val['fail_status'] == 0) {
                $val['fail_status_view'] = '未处理';
            } else {
                $val['fail_status_view'] = '已处理';
            }
            switch ($val['order_status']) {
                case 1 :
                    $val['order_status_view'] = "待确认";
                    break;
                case 2 :
                    if ($val['is_buy'] == 0) {
                        $val['order_status_view'] = "待拍单";
                    } else {
                        $val['order_status_view'] = "待发货";
                    }
                    break;
                case 3 :
                    $val['order_status_view'] = "已发货";
                    break;
                case 4 :
                    $val['order_status_view'] = "已取消";
                    break;
                case 5 :
                    $val['order_status_view'] = "已完成";
                    break;
                case 6 :
                    $val['order_status_view'] = "退货中";
                    break;
                case  7:
                    $val['order_status_view'] = "退货成功";
                    break;
                case 8 :
                    $val['order_status_view'] = "退货失败";
                    break;
                case 9 :
                    $val['order_status_view'] = "异常订单";
                    break;
                default:
                    $val['order_status_view'] = "未知";
                    break;
            }
        }
        $key_name  = array(
            'order_id'          => '订单编号',
            'product_name'      => '商品名称',
            'order_source'      => '订单来源',
            'buy_num'           => '购买数量',
            'total_amount'      => '支付金额',
            'post_receiver'     => '收件人',
            'post_tel'          => '收件电话',
            'post_address'      => '收件地址',
            'order_status_view' => '订单状态',
            'pay_type'          => '支付方式',
            'pay_time'          => '下单时间',
            'finish_time'       => '完成时间',
            'buyer_words'       => '用户留言',
            'seller_words'      => '商户备注',
            'logistics_name'    => '快递信息',
            'cancel_remark'     => '取消原因',
            'fail_status_view'  => '处理状态',
            'remark'            => '管理员备注',
        );
        $file_name = '放心购订单-下载时间' . date('Y-m-d');
        if ($pay_time) {
            $file_name = "放心购订单-下单时间{$pay_time}";
        }
        if ($finish_time) {
            $file_name = "放心购订单-完成时间{$finish_time}";
        }
        download_xls($data, $key_name, $file_name);
    }

    /**
     * 订单抓取
     */
    public function updateOrder() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $url           = 'https://fxg.jinritemai.com/order/torder/searchlist';
        $time          = time() * 1000;
        $start_time    = '2018-06-22';
        $end_time      = date('Y-m-d');
        $page          = I('post.page', 0, 'int');
        $page_size     = 100;
        $account_info  = M('account')->find(1);
        $param         = array('start_time' => $start_time, 'end_time' => $end_time, 'order' => 'create_time', 'is_desc' => 'asc', 'page' => $page, 'pageSize' => $page_size, '__token' => $account_info['account_token'], '_' => $time);
        $cookie        = $account_info['account_cookie'];
        $param['page'] = $page;
        $res           = $this->_get($url, $param, $cookie);
        $data          = json_decode($res, true);
        if (!isset($data['code']) || $data['code'] != 0) {
            $this->error($data['msg']);
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
                $cos_ratio = M('item')->where(array('item_id' => $order['product_id']))->getField('cos_ratio');
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
            $model = M('order');
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
                $this->success("第{$page}页订单抓取并处理成功");
            } catch (\Exception $e) {
                $model->rollback();
                $this->error("数据入库失败->" . $e->getMessage());
            }
        } else {
            $this->success('数据已全部抓取完成');
        }
    }
}