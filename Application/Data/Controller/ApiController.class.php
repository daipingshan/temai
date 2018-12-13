<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/12/10
 * Time: 14:10
 */

namespace Data\Controller;

/**
 * Class ApiController
 *
 * @package Data\Controller
 */
class ApiController extends CommonController {

    /**
     * 头条插件登录
     */
    public function login() {
        header('Access-Control-Allow-Origin:*');
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        if (empty($username)) {
            $this->_output('账号不能为空！');
        }
        if (empty($password)) {
            $this->_output('密码不能为空！');
        }
        $where = array(
            'name'     => $username,
            'password' => encrypt_pwd($password)
        );
        $info  = M('tmuser')->where($where)->find();
        if (empty($info)) {
            $this->_output('账号或密码错误！');
        }
        $this->_output('登录成功', 'success', array('user_id' => $info['id']));
    }

    /**
     * 设置账号COOKIE
     */
    public function setTopLineCookie() {
        header('Access-Control-Allow-Origin:*');
        $cookie   = I('post.cookie', '', 'trim');
        $admin_id = I('post.admin_id', 0, 'int');
        if (empty($cookie)) {
            $this->_output('cookie不能为空！');
        }
        $url = "https://mp.toutiao.com/core/article/new_article/?article_type=3&format=json";
        $res = $this->_get($url, array(), $cookie);
        if ($res === false) {
            $this->_output('cookie验证失败！');
        }
        $res = json_decode($res, true);
        if (!isset($res['user_name']) || !isset($res['media_id'])) {
            $this->_output('cookie无效！');
        }
        $username = $res['user_name'];
        $media_id = $res['media']['creator_id'];
        $id       = M('top_line_account')->where(array('media_id' => $media_id))->getField('id');
        if ($id > 0) {
            $res = M('top_line_account')->where(array('id' => $id))->save(array('cookie' => $cookie, 'update_time' => time()));
        } else {
            $have_user = M('top_line_account')->where(array('username' => $username))->count();
            while ($have_user) {
                $username  = $username . rand(1000, 9999);
                $have_user = M('user')->where(array('username' => $username))->count();
            }
            $data = array('username' => $username, 'cookie' => $cookie, 'add_time' => time(), 'update_time' => time(), 'admin_id' => $admin_id, 'media_id' => $media_id);
            $res  = M('top_line_account')->add($data);
        }
        if ($res !== false) {
            if ($id == 0) {
                $admin_info = M('tmuser')->find($admin_id);
                M('tmuser')->where(array('id' => $admin_id))->setField('top_line_account_ids', $admin_info['top_line_account_ids'] ? $admin_info['top_line_account_ids'] . "," . $res : $res);
            }
            S('top_line_account', null);
            S('top_line_cookie_media', null);
            $this->_output('保存成功', 'success');
        } else {
            $this->_output('保存失败', 'success');
        }
    }

    /**
     * @param string $msg
     * @param string $code
     * @param array $data
     */
    protected function _output($msg = '请求错误', $code = 'fail', $data = array()) {
        $json_data = array('code' => $code, 'msg' => $msg, 'data' => $data);
        ob_clean();
        die(json_encode($json_data));
    }
}