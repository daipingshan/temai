<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/3
 * Time: 15:13
 */

namespace Admin\Controller;

/**
 * Class UserController
 *
 * @package Admin\Controller
 */
class UserController extends CommonController {


    /**
     * 用户列表
     */
    public function index() {
        $username = I('get.username', '', 'trim');
        $where    = array();
        if ($username) {
            $where['name|zzname'] = array('like', "%{$username}%");
        }
        $count  = M('tmuser')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('tmuser')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加账号
     */
    public function add() {
        $this->assign('type', 'add');
        $this->display('save');
    }

    /**
     * 添加并保存账号
     */
    public function addAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $username     = I('post.name', '', 'trim');
        $password     = I('post.password', '', 'trim');
        $real_name    = I('post.real_name', '', 'trim');
        $pid          = I('post.pid', '', 'trim');
        $source_id    = I('post.source_id', '', 'trim');
        $group_id     = I('post.group_id', 0, 'int');
        $is_outer_net = I('post.is_outer_net', 0, 'int');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$real_name) {
            $this->error('真实姓名不能为空！');
        }
        if (!$password) {
            $this->error('密码不能为空！');
        }
        if (!$group_id) {
            $this->error('请给账号授权！');
        }
        $data = array('name' => $username, 'is_outer_net' => $is_outer_net, 'password' => encrypt_pwd($password), 'zzname' => $real_name, 'pid' => $pid, 'source_id' => $source_id, 'group_id' => $group_id);
        $res  = M('tmuser')->add($data);
        if ($res) {
            S('sale_user_data', null);
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 修改账号
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('tmuser')->find($id);
        $this->assign('info', $info);
        $this->assign('type', 'update');
        $this->display('save');
    }

    /**
     * 修改并保存账号
     */
    public function updateAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $username     = I('post.name', '', 'trim');
        $password     = I('post.password', '', 'trim');
        $real_name    = I('post.real_name', '', 'trim');
        $pid          = I('post.pid', '', 'trim');
        $source_id    = I('post.source_id', '', 'trim');
        $group_id     = I('post.group_id', 0, 'int');
        $is_outer_net = I('post.is_outer_net', 0, 'int');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$real_name) {
            $this->error('真实姓名不能为空！');
        }
        if (!$group_id) {
            $this->error('请给账号授权！');
        }
        $data = array('name' => $username, 'is_outer_net' => $is_outer_net, 'zzname' => $real_name, 'pid' => $pid, 'id' => $id, 'source_id' => $source_id, 'group_id' => $group_id);
        if ($password) {
            $data['password'] = encrypt_pwd($password);
        }
        $res = M('tmuser')->save($data);
        if ($res !== false) {
            S('sale_user_data', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 删除账号
     */
    public function deleteAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('tmuser')->delete($id);
        if ($res) {
            S('sale_user_data', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 用户授权
     */
    public function auth() {
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', '', 'trim');
        $info = M('tmuser')->find($id);
        if ($type == 'sale') {
            $user_auth = explode(',', $info['sale_account_ids']);
            $auth_list = M('sale_account')->where(array('id' => array('neq', 1)))->select();
        } else if ($type == 'top_line') {
            $user_auth = explode(',', $info['top_line_account_ids']);
            $auth_list = M('top_line_account')->where(array('id' => array('neq', 1)))->select();
        } else {
            $user_auth = explode(',', $info['shop_ids']);
            $auth_list = M('shop')->field('*,name as username')->select();
        }
        $this->assign(array('type' => $type, 'id' => $id, 'auth_list' => $auth_list, 'user_auth' => $user_auth));
        $this->display();
    }

    /**
     * 账号授权
     */
    public function userAuth() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $type    = I('post.type', 'sale', 'trim');
        $auth_id = I('post.auth_id', '', 'trim');
        if (!$id) {
            $this->error('账号不存在！');
        }
        if ($type == 'sale') {
            $save_data = array('id' => $id, 'sale_account_ids' => implode(',', $auth_id));
        } else if ($type == 'top_line') {
            $save_data = array('id' => $id, 'top_line_account_ids' => implode(',', $auth_id));
        } else {
            $save_data = array('id' => $id, 'shop_ids' => implode(',', $auth_id));
        }
        $res = M('tmuser')->save($save_data);
        if ($res !== false) {
            $this->success('授权成功');
        } else {
            $this->error('授权失败！');
        }
    }

    /**
     * 修改密码
     */
    public function updatePass() {
        $this->display();
    }

    /**
     * 保存密码
     */
    public function savePass() {
        if (!IS_AJAX) {
            $this->error('非法请求');
        }
        $password = I('post.password', '', 'trim');
        if (empty($password)) {
            $this->error('密码不能为空！');
        }
        $res = M('tmuser')->where(array('id' => $this->user_id))->save(array('password' => encrypt_pwd($password)));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

}