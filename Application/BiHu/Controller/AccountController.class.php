<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/19
 * Time: 8:55
 */

namespace BiHu\Controller;


class AccountController extends CommonController {

    /**
     * 账号列表
     */
    public function index() {
        $username = I('get.username', '', 'trim');
        $where    = array();
        if ($username) {
            $where['username'] = array('like', "%{$username}%");
        }
        $count  = M('account')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('account')->where($where)->limit($limit)->order('id desc')->select();
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
        $this->display('save');
    }

    /**
     * 修改账号
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('account')->find($id);
        $this->assign('info', $info);
        $this->display('save');
    }

    /**
     * 修改并保存账号
     */
    public function saveData() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id           = I('post.id', 0, 'int');
        $user_id      = I('post.user_id', 0, 'int');
        $username     = I('post.username', '', 'trim');
        $cookie       = I('post.cookie', '', 'trim');
        $access_token = I('post.access_token', '', 'trim');
        if (!$user_id) {
            $this->error('账号ID不能为空！');
        }
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$access_token) {
            $this->error('账号token不能为空！');
        }
        if (!$cookie) {
            $this->error('账号cookie不能为空！');
        }
        $data = array('user_id' => $user_id, 'username' => $username, 'cookie' => $cookie, 'access_token' => $access_token, 'update_time' => time());
        if ($id) {
            $data['id'] = $id;
            $res        = M('account')->save($data);
        } else {
            $data['create_time'] = time();
            $res                 = M('account')->add($data);
        }
        if ($res !== false) {
            $this->_getAccount();
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
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
            $this->error('评论不存在！');
        }
        $res = M('account')->delete($id);
        if ($res) {
            $this->_getAccount();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 缓存作者
     */
    protected function _getAccount() {
        $author_id = S('BiHu_account');
        if ($author_id) {
            S('BiHu_account', null);
        }
        $data = M('account')->getField('user_id,access_token', true);
        S('BiHu_account', json_encode($data), 10 * 365 * 86400);
    }
}