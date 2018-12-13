<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/11/9
 * Time: 8:58
 */

namespace Fxg\Controller;


class OrderErrorController extends CommonController {

    /**
     * 发货订单异常数据
     */
    public function index() {
        if (IS_AJAX) {
            $order_id = I('get.order_id', '', 'trim');
            $type     = I('get.type', '', 'trim');
            $page     = I('get.page', 1, 'int');
            $limit    = I('get.limit', 10, 'int');
            $where    = array();
            if ($order_id) {
                $where['order_id'] = $order_id;
            }
            if ($type) {
                $where['type'] = $type;
            }
            $model = M('order_error');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$val) {
                if ($val['type'] == 1) {
                    $val['type_name'] = '订单发货';
                } else if ($val['type'] == 2) {
                    $val['type_name'] = '订单确认';
                } else {
                    $val['type_name'] = '重新发货';
                }
                $val['error_info'] = str_replace("\r\n", "<br>", $val['error_info']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }
}