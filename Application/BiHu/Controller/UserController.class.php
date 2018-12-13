<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/3
 * Time: 15:13
 */

namespace BiHu\Controller;

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
            $where['username'] = array('like', "%{$username}%");
        }
        $count  = M('admin')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('admin')->where($where)->limit($limit)->order('id desc')->select();
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
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$password) {
            $this->error('密码不能为空！');
        }
        $data = array('username' => $username, 'password' => encrypt_pwd($password), 'create_time' => time());
        $res  = M('admin')->add($data);
        if ($res) {
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
        $info = M('admin')->find($id);
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
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        $data = array('username' => $username);
        if ($password) {
            $data['password'] = encrypt_pwd($password);
        }
        $res = M('admin')->save($data);
        if ($res !== false) {
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
        $res = M('admin')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
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
        $res = M('admin')->where(array('id' => $this->user_id))->save(array('password' => encrypt_pwd($password)));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

}