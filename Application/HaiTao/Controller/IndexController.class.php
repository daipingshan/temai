<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/11
 * Time: 15:56
 */

namespace HaiTao\Controller;


class IndexController extends CommonController {

    /**
     * 后台首页
     */
    public function index() {
        $this->display();
    }

    /**
     * 保存密码
     */
    public function updatePass() {
        if (!IS_AJAX) {
            $this->error('非法请求');
        }
        $password = I('post.password', '', 'trim');
        if (empty($password)) {
            $this->error('密码不能为空！');
        }
        $res = M('admin')->where(array('id' => $this->user_id))->save(array('password' => encrypt_pwd($password)));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 保存账号信息
     */
    public function updateAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求');
        }
        $cookie = I('post.cookie', '', 'trim');
        $token  = I('post.token', '', 'trim');
        if (empty($cookie) && empty($token)) {
            $this->error('cookie或token不能同时为空！');
        }
        $data = array('update_time' => time());
        if ($cookie) {
            $data['account_cookie'] = $cookie;
        }
        if ($token) {
            $data['account_token'] = $token;
        }
        $res = M('account')->where(array('id' => 1))->save($data);
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

}