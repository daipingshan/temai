<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:24
 */

namespace HaiTao\Controller;


use Common\Controller\CommonBaseController;

/**
 * 用户登录
 * Class LoginController
 *
 * @package Admin\Controller
 */
class LoginController extends CommonBaseController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 用户登录
     */
    public function index() {
        if (session('ht_user_info')) {
            $this->redirect('Index/index');
        }
        $this->display();
    }

    /**
     * 登录请求
     */
    public function doLogin() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        if (empty($username)) {
            $this->error('账号不能为空！');
        }
        if (empty($password)) {
            $this->error('密码不能为空！');
        }
        $info = M('admin')->where(array('username' => $username, 'password' => encrypt_pwd($password)))->find();
        if (empty($info)) {
            $this->error('账号或密码错误！');
        }
        session('ht_user_id', $info['id']);
        session('ht_user_info', $info);
        $this->success('登录成功');
    }

    /**
     * 用户退出
     */
    public function logout() {
        session_destroy();
        $this->redirect('index');
    }
}