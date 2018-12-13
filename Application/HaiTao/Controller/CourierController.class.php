<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/13
 * Time: 16:29
 */

namespace HaiTao\Controller;


class CourierController extends CommonController {

    /**
     * 商品列表
     */
    public function index() {
        if (IS_AJAX) {
            $username       = I('get.username', '', 'trim');
            $alipay_account = I('get.alipay_account', '', 'trim');
            $mobile         = I('get.mobile', '', 'trim');
            $page           = I('get.page', 1, 'int');
            $limit          = I('get.limit', 10, 'int');
            $where          = array();
            if (!empty($username)) {
                $where['username'] = array('like', "%{$username}%");
            }
            if (!empty($alipay_account)) {
                $where['alipay_account'] = $alipay_account;
            }
            if (!empty($mobile)) {
                $where['mobile'] = $mobile;
            }
            $model = M('courier');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('status desc')->select();
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d', $val['create_time']);
            }
            $this->output($data, $count);
        } else {
            $partner = M('partner')->where(array('status' => 1))->getField('id,name');
            $this->assign('partner', $partner);
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
        $info = M('courier')->find($id);
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
        $id             = I('post.id', 0, 'int');
        $username       = I('post.username', '', 'trim');
        $alipay_account = I('post.alipay_account', '', 'trim');
        $mobile         = I('post.mobile', '', 'trim');
        $create_time    = I('post.create_time', '', 'trim');
        if (empty($username)) {
            $this->error('真实姓名不能为空！');
        }
        if (empty($alipay_account)) {
            $this->error('支付宝账号不能为空！');
        }
        if (empty($mobile)) {
            $this->error('手机号码不能为空！');
        }
        $data = array('username' => $username, 'alipay_account' => $alipay_account, 'mobile' => $mobile);
        if ($id > 0) {
            $res = M('courier')->where(array('id' => $id))->save($data);
        } else {
            $data['create_time'] = $create_time ? strtotime($create_time) : time();
            $res                 = M('courier')->add($data);
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
        $status = M('courier')->getFieldById($id, 'status');
        $res    = M('courier')->where(array('id' => $id))->save(array('status' => $status == 1 ? 0 : 1));
        if ($res !== false) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 绑定供货商
     */
    public function bindPartner() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $courier_id = I('post.courier_id', 0, 'int');
        $partner_id = I('post.partner_id', 0, 'int');
        if (empty($courier_id)) {
            $this->error('请求参数不完整！');
        }
        if (empty($partner_id)) {
            $this->error('请选择供货商！');
        }
        $count = M('courier_partner')->where(array('courier_id' => $courier_id, 'partner_id' => $partner_id))->count('id');
        if ($count) {
            $this->error('该快递员已绑定改供货商信息，不能重复绑定！');
        }
        $data = array('courier_id' => $courier_id, 'partner_id' => $partner_id);
        $res  = M('courier_partner')->add($data);
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
        $id    = I('get.courier_id', 0, 'int');
        $field = "c_p.id,p.name,p.address,p.mobile,c_p.status";
        $data  = M('courier_partner')->alias('c_p')->join('left join ht_partner p ON p.id = c_p.partner_id')->field($field)->where(array('c_p.courier_id' => $id))->order('c_p.status desc')->select();
        $this->assign('data', $data);
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
        $status = M('courier_partner')->getFieldById($id, 'status');
        $res    = M('courier_partner')->where(array('id' => $id))->save(array('status' => $status == 1 ? 0 : 1));
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}