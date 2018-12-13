<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:24
 */

namespace Admin\Controller;


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
        if (session('admin_user_info')) {
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
        $where = array(
            'name'     => $username,
            'password' => encrypt_pwd($password)
        );
        $info  = M('tmuser')->where($where)->find();
        if (empty($info)) {
            $this->error('账号或密码错误！');
        }
        $ip_data        = array('49.221.62', '1.86.244', '36.47.137', '113.134.75', '183.1.103', '183.1.102', '127.0.0');
        $client_ip      = get_client_ip();
        $client_ip_data = explode('.', $client_ip);
        $ip             = $client_ip_data[0] . '.' . $client_ip_data[1] . '.' . $client_ip_data[2];
        if (!in_array($ip, $ip_data) && $info['is_outer_net'] == 0) {
            $this->_addLogs('login.log', array('username' => $username, 'password' => $password));
            $this->error('此账户不能在外网登陆！');
        }
        session('admin_user_id', $info['id']);
        session('admin_user_info', $info);
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
