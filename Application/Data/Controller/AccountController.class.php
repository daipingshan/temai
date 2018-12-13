<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/5
 * Time: 10:06
 */

namespace Data\Controller;

/**
 * Class AccountController
 *
 * @package Data\Controller
 */
class AccountController extends CommonController {

    /**
     * 更新账号Cookie
     */
    public function updateSaleCookie() {
        $username = I('post.username', '', 'trim');
        $cookie   = I('post.cookie', '', 'trim');
        if (!$username) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号名称不能为空！'));
        }
        if (!$cookie) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号cookie不能为空！'));
        }
        $id = M('sale_account')->where(array('username' => $username))->getField('id');
        if (!$id) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号不存在！'));
        }
        $cookie = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($cookie)));;
        $data = array('id' => $id, 'cookie' => $cookie, 'update_time' => time());
        $res  = M('sale_account')->save($data);
        if ($res !== false) {
            if ($id == 1) {
                S('sale_cookie', null);
            }
            $this->ajaxReturn(array('code' => 1, 'msg' => '更新成功！'));
        } else {
            $this->ajaxReturn(array('code' => 0, 'msg' => '更新失败！'));
        }
    }
}