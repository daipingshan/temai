<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/28
 * Time: 8:53
 */

namespace Data\Controller;

/**
 * Class UserController
 *
 * @package Data\Controller
 */
class UserController extends CommonController {

    /**
     * 更新账号Cookie
     */
    public function updateSaleCookie() {
        $username = I('post.username', '', 'trim');
        $user_id  = I('post.userid', 0, 'int');
        $cookie   = I('post.cookie', '', 'trim');
        if (!$username && !$user_id) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号名称或账号ID不能同时为空！'));
        }
        if (!$cookie) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号cookie不能为空！'));
        }
        if ($user_id) {
            $id = M('sale_account')->where(array('userid' => $user_id))->getField('id');
        } else {
            $id = M('sale_account')->where(array('username' => $username))->getField('id');
        }
        if (!$id) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '账号不存在！'));
        }
        $cookie = base64_decode(str_replace(array('%2b', ' '), '+', urldecode($cookie)));;
        $data = array('id' => $id, 'cookie' => $cookie, 'update_time' => time());
        $res  = M('sale_account')->save($data);
        if ($res !== false) {
            S('sale_account', null);
            if ($id == 1) {
                S('sale_cookie', null);
            }
            $this->ajaxReturn(array('code' => 1, 'msg' => '更新成功！'));
        } else {
            $this->ajaxReturn(array('code' => 0, 'msg' => '更新失败！'));
        }
    }
}