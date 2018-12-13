<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/12
 * Time: 9:23
 */

namespace HaiTao\Controller;

/**
 * Class PartnerController
 *
 * @package HaiTao\Controller
 */
class PartnerController extends CommonController {

    /**
     * 供货商列表
     */
    public function index() {
        if (IS_AJAX) {
            $name    = I('get.name', '', 'trim');
            $keyword = I('get.keyword', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 10, 'int');
            $where   = array();
            if (!empty($name)) {
                $where['name'] = array('like', "%{$name}%");
            }
            if (!empty($keyword)) {
                $where['mobile|wang_wang|wei_xin|qq'] = array('like', "%{$keyword}%");
            }
            $model = M('partner');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('status desc')->select();
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d', $val['create_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 添加供货商
     */
    public function add() {
        $this->display('save');
    }

    /**
     * 添加供货商
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('partner')->find($id);
        $this->assign('info', $info);
        $this->display('save');
    }

    /**
     * 保存供货商信息
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id          = I('post.id', 0, 'int');
        $name        = I('post.name', '', 'trim');
        $address     = I('post.address', '', 'trim');
        $mobile      = I('post.mobile', '', 'trim');
        $wang_wang   = I('post.wang_wang', '', 'trim');
        $wei_xin     = I('post.wei_xin', '', 'trim');
        $qq          = I('post.qq', '', 'trim');
        $create_time = I('post.create_time', '', 'trim');
        if (empty($name)) {
            $this->error('供货商名称不能为空！');
        }
        if (empty($address)) {
            $this->error('供货商地址不能为空！');
        }
        if (empty($mobile)) {
            $this->error('供货商电话不能为空！');
        }
        if (empty($wang_wang) && empty($wei_xin) && empty($qq)) {
            $this->error('供货商旺旺/微信/QQ最少要填写一个！');
        }
        $data = array('name' => $name, 'address' => $address, 'mobile' => $mobile, 'wang_wang' => $wang_wang, 'wei_xin' => $wei_xin, 'qq' => $qq);
        if ($id > 0) {
            $res = M('partner')->where(array('id' => $id))->save($data);
        } else {
            $data['create_time'] = $create_time ? strtotime($create_time) : time();
            $res                 = M('partner')->add($data);
        }
        if ($res !== false) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 设置供货商状态
     */
    public function setStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (empty($id)) {
            $this->error('请求参数不完整！');
        }
        $status = M('partner')->getFieldById($id, 'status');
        $res    = M('partner')->where(array('id' => $id))->save(array('status' => $status == 1 ? 0 : 1));
        if ($res !== false) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 查看供货商对应商品
     */
    public function getItem() {
        $id    = I('get.id', 0, 'int');
        $field = "i.item_id as item_id,i.title as title,i.price as price,i_p.price as from_price";
        $data  = M('item_partner')->alias('i_p')->join('left join ht_item i ON i.item_id = i_p.item_id')->field($field)->where(array('i_p.partner_id' => $id, 'i_p.status' => 1, 'i.status' => 1))->select();
        if (count($data) > 0) {
            $info = '';
            foreach ($data as $val) {
                $info .= "商品编号：{$val['item_id']}，商品标题：{$val['title']}，商品价格：{$val['price']}，供货价格：{$val['from_price']}<br>";
            }
        } else {
            $info = "该供货商无供货商品！";
        }
        $this->success($info);
    }

    /**
     * 查看供货商对应快递员信息
     */
    public function getCourier() {
        $id    = I('get.id', 0, 'int');
        $field = "c.username as username,c.alipay_account as alipay_account,c.mobile as mobile";
        $data  = M('courier_partner')->alias('c_p')->join('left join ht_courier c ON c.id = c_p.courier_id')->field($field)->where(array('c_p.partner_id' => $id, 'c_p.status' => 1, 'c.status' => 1))->select();
        if (count($data) > 0) {
            $info = '';
            foreach ($data as $val) {
                $info .= "真实姓名：{$val['username']}，支付宝账号：{$val['alipay_account']}，联系电话：{$val['mobile']}<br>";
            }
        } else {
            $info = "该供货商无对应快递员！";
        }
        $this->success($info);
    }
}