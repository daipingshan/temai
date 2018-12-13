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
class AuthorController extends CommonController {


    /**
     * 用户列表
     */
    public function index() {
        $username = I('get.author_name', '', 'trim');
        $where    = array();
        if ($username) {
            $where['author_name'] = array('like', "%{$username}%");
        }
        $count  = M('author')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('author')->where($where)->limit($limit)->order('id desc')->select();
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
        $info = M('author')->find($id);
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
        $id          = I('post.id', 0, 'int');
        $author_id   = I('post.author_id', 0, 'int');
        $author_name = I('post.author_name', '', 'trim');
        $author_url  = I('post.author_url', '', 'trim');
        if (!$author_id) {
            $this->error('作者ID不能为空！');
        }
        if (!$author_name) {
            $this->error('作者名称不能为空！');
        }
        if (!$author_url) {
            $this->error('作者网址不能为空！');
        }
        $data = array('author_id' => $author_id, 'author_name' => $author_name, 'author_url' => $author_url);
        if ($id) {
            $data['id'] = $id;
            $res        = M('author')->save($data);
        } else {
            $data['create_time'] = time();
            $res                 = M('author')->add($data);
        }
        if ($res !== false) {
            $this->_getUpAuthor();
            $this->_getAuthor();
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
            $this->error('作者不存在！');
        }
        $res = M('author')->delete($id);
        if ($res) {
            $this->_getUpAuthor();
            $this->_getAuthor();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 缓存作者
     */
    protected function _getAuthor() {
        $author_id = S('BiHu_author');
        if ($author_id) {
            S('BiHu_author', null);
        }
        $data = M('author')->getField('author_id,author_name', true);
        S('BiHu_author', json_encode($data), 10 * 365 * 86400);
    }

    /**
     * 缓存点赞作者
     */
    protected function _getUpAuthor() {
        $author_id = S('BiHu_up_author');
        if ($author_id) {
            S('BiHu_up_author', null);
        }
        $data = M('author')->where(array('is_up' => 1))->getField('author_id', true);
        S('BiHu_up_author', json_encode($data), 10 * 365 * 86400);
    }

}