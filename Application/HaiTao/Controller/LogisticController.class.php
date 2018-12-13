<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/22
 * Time: 11:49
 */

namespace HaiTao\Controller;

/**
 * Class LogisticController
 *
 * @package HaiTao\Controller
 */
class LogisticController extends CommonController {

    /**
     * 快递信息列表
     */
    public function index() {
        if (IS_AJAX) {
            $name    = I('get.name', '', 'trim');
            $mobile  = I('get.mobile', '', 'trim');
            $is_down = I('get.is_down', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 10, 'int');
            //$where    = array('o.order_status' => 2);
            $where = array();
            if (!empty($name)) {
                $where['name'] = $name;
            }
            if (!empty($keyword)) {
                $where['mobile'] = $mobile;
            }
            if (!empty($is_down)) {
                $where['is_down'] = 1;
            }
            $model = M('logistics');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$val) {
                //$where                 = array('post_receiver' => $val['name'], 'post_tel' => $val['mobile'], 'order_status' => 2);
                $where           = array('post_receiver' => $val['name'], 'post_tel' => $val['mobile']);
                $val['order_id'] = M('order')->where($where)->getField('order_id');
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 下载顺丰订单
     */
    public function downLogistic() {
        $model = M('logistics');
        $where = array('is_down' => 0);
        $data  = $model->where($where)->select();
        $model->where($where)->save(array('is_down' => 1));
        foreach ($data as &$val) {
            //$where                 = array('post_receiver' => $val['name'], 'post_tel' => $val['mobile'], 'order_status' => 2);
            $where                 = array('post_receiver' => $val['name'], 'post_tel' => $val['mobile']);
            $val['order_id']       = M('order')->where($where)->getField('order_id');
            $val['logistics_name'] = 'shunfeng';
        }
        $key_name  = array(
            'order_id'       => '订单号',
            'logistics_name' => '快递名称',
            'logistics_code' => '快递单号',
            'name'           => '收货人姓名',
            'address'        => '收货地址',
        );
        $file_name = date('Y-m-d') . '批量发货';
        download_xls($data, $key_name, $file_name);
    }
}