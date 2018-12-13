<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/12
 * Time: 15:48
 */

namespace HaiTao\Controller;

/**
 * 商品管理
 * Class ItemController
 *
 * @package HaiTao\Controller
 */
class ItemController extends CommonController {

    /**
     * 商品列表
     */
    public function index() {
        if (IS_AJAX) {
            $item_id = I('get.item_id', '', 'trim');
            $keyword = I('get.keyword', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 10, 'int');
            $where   = array();
            if (!empty($item_id)) {
                $where['item_id'] = array('like', "%{$item_id}%");
            }
            if (!empty($keyword)) {
                $where['title'] = array('like', "%{$keyword}%");
            }
            $model = M('item');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('status desc')->select();
            $this->output($data, $count);
        } else {
            $partner = M('partner')->where(array('status' => 1))->getField('id,name');
            $this->assign('partner', $partner);
            $this->display();
        }
    }

    /**
     * 绑定供货商
     */
    public function bindPartner() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id    = I('post.item_id', '', 'trim');
        $partner_id = I('post.partner_id', 0, 'int');
        $name       = I('post.name', '', 'trim');
        $price      = I('post.price', '', 'trim');
        $buy_url    = I('post.buy_url', '', 'trim');
        if (empty($item_id)) {
            $this->error('请求参数不完整！');
        }
        if (empty($partner_id)) {
            $this->error('请选择供货商！');
        }
        if (empty($name)) {
            $this->error('规格不能为空！');
        }
        if (empty($price)) {
            $this->error('价格不能为空！');
        }
        if (count($name) != count($price)) {
            $this->error('规格数量与价格数量不对应！');
        }
        if (empty($buy_url)) {
            $this->error('下单地址不能为空！');
        }
        $content = array();
        foreach ($name as $key => $val) {
            $content[] = array('name' => $val, 'price' => $price[$key]);
        }
        if (empty($content)) {
            $this->error('规格-价格不能为空');
        }
        $count = M('item_partner')->where(array('item_id' => $item_id, 'partner_id' => $partner_id))->count('id');
        if ($count) {
            $this->error('该商品已绑定改供货商信息，不能重复绑定！');
        }
        $data = array('item_id' => $item_id, 'partner_id' => $partner_id, 'content' => json_encode($content), 'buy_url' => $buy_url);
        $res  = M('item_partner')->add($data);
        if ($res) {
            $this->success('绑定成功');
        } else {
            $this->error('绑定失败');
        }
    }

    /**
     * 查看供货商信息
     */
    public function checkPartner() {
        $id    = I('get.item_id', '', 'trim');
        $field = "i_p.id,p.name,p.address,p.mobile,i_p.status,i_p.content,i_p.buy_url";
        $data  = M('item_partner')->alias('i_p')->join('left join ht_partner p ON p.id = i_p.partner_id')->field($field)->where(array('i_p.item_id' => $id))->order('i_p.status desc')->select();
        foreach ($data as &$val) {
            $val['content_view'] = json_decode($val['content'], true);
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 编辑供货商信息
     */
    public function updateItemPartner() {
        $id              = I('get.id', 0, 'int');
        $info            = M('item_partner')->find($id);
        $info['content'] = json_decode($info['content'], true);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 解除绑定供货商信息
     */
    public function setPartnerStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (empty($id)) {
            $this->error('请求参数不完整！');
        }
        $status = M('item_partner')->getFieldById($id, 'status');
        $res    = M('item_partner')->where(array('id' => $id))->save(array('status' => $status == 1 ? 0 : 1));
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑商品绑定供货商信息
     */
    public function saveItemPartner() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', '', 'trim');
        $name    = I('post.name', '', 'trim');
        $price   = I('post.price', '', 'trim');
        $buy_url = I('post.buy_url', '', 'trim');
        if (empty($id)) {
            $this->error('请求参数不完整！');
        }
        if (empty($name)) {
            $this->error('规格不能为空！');
        }
        if (empty($price)) {
            $this->error('价格不能为空！');
        }
        if (count($name) != count($price)) {
            $this->error('规格数量与价格数量不对应！');
        }
        if (empty($buy_url)) {
            $this->error('下单地址不能为空！');
        }
        $content = array();
        foreach ($name as $key => $val) {
            $content[] = array('name' => $val, 'price' => $price[$key]);
        }
        if (empty($content)) {
            $this->error('规格-价格不能为空');
        }
        $data = array('id' => $id, 'content' => json_encode($content), 'buy_url' => $buy_url);
        $res  = M('item_partner')->save($data);
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }


}