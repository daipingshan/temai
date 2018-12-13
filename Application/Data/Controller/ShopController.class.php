<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/10/30
 * Time: 10:19
 */

namespace Data\Controller;

/**
 * Class ShopController
 *
 * @package Data\Controller
 */
class ShopController extends CommonController {

    /**
     * 同步小店选品
     */
    public function shopItem() {
        $shop_data = M('shop')->getField('id,name');
        $time      = date('Y-m-d', strtotime('-1 days'));
        $where     = array('top_line_article_behot_time' => array('between', array(strtotime($time), strtotime($time) + 86399)));
        foreach ($shop_data as $shop_id => $shop_name) {
            $add_data = array();
            if ($shop_name == '推荐小店') {
                $shop_product = M('shop_product_tuijian');
                $shop_items   = M('shop_items_tuijian');
            } else {
                $shop_product = M('shop_product');
                $shop_items   = M('shop_items');
            }
            $shop_goods_id = $shop_product->where(array('shop_id' => $shop_id))->getField('platform_sku_id', true);
            $item_data     = M('temai_items')->where(array('shop_goods_id' => array('in', $shop_goods_id)))->where($where)->group('description')->select();
            foreach ($item_data as $val) {
                unset($val['id']);
                $id = $shop_items->where(array('md5_desc' => md5($val['shop_goods_id'] . $val['description'])))->getField('id');
                if ($id > 0) {
                    $shop_items->where(array('id' => $id))->save($val);
                } else {
                    $val['shop_id']   = $shop_id;
                    $val['shop_name'] = $shop_name;
                    $val['md5_desc']  = md5($val['shop_goods_id'] . $val['description']);
                    $add_data[]       = $val;
                }
            }
            $data = array_chunk($add_data, 999);
            foreach ($data as $v) {
                $shop_items->addAll($v);
            }
            $count        = count($item_data);
            $add_count    = count($add_data);
            $update_count = $count - $add_count;
            $this->_addLogs("shop_item.log", date('Y-m-d H:i:s') . "{$shop_name}-数据同步完成，更新{$update_count}条，添加{$add_count}条!");
        }
    }

}