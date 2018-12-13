<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/15
 * Time: 16:28
 */

namespace Data\Controller;

/**
 * Class LogisticController
 *
 * @package Data\Controller
 */
class LogisticController extends CommonController {

    /**
     * 抓取快递单号
     */
    public function index() {
        $json        = json_decode(I('post.json', '', 'trim'), true);
        $return_data = array('status' => 0, 'info' => 'ok');
        if (empty($json) || count($json) == 0) {
            $return_data['info'] = '请输入参数不合法！';
            $this->ajaxReturn($return_data);
        }
        $data = array();
        foreach ($json as $val) {
            if ($val['logistics_order_id'] && $val['logistics_code'] && $val['total_money'] && $val['logistics_money'] && $val['name'] && $val['mobile'] && $val['address']) {
                $data[$val['logistics_order_id']] = $val;
            }
        }
        if (count($data) == 0) {
            $return_data['info'] = '请求数据异常，无法解析！';
            $this->ajaxReturn($return_data);
        }
        $model     = M('logistics', 'ht_', 'FXG_DB');
        $have_data = $model->where(array('logistics_order_id' => array('in', array_keys($data))))->getField('logistics_order_id', true);
        foreach ($have_data as $logistics_order_id) {
            unset($data[$logistics_order_id]);
        }
        $return_data['status'] = 1;
        if (count($data) == 0) {
            $return_data['info'] = '数据处理成功，所有数据均已添加';
            $this->ajaxReturn($return_data);
        } else {
            $model->addAll(array_values($data));
            $return_data['info'] = '数据处理成功，添加成功';
            $this->ajaxReturn($return_data);
        }

    }
}