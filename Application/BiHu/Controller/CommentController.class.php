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
class CommentController extends CommonController {


    /**
     * 用户列表
     */
    public function index() {
        $username = I('get.content', '', 'trim');
        $where    = array();
        if ($username) {
            $where['content'] = array('like', "%{$username}%");
        }
        $count  = M('comment')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('comment')->where($where)->limit($limit)->order('id desc')->select();
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
        $info = M('comment')->find($id);
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
        $id      = I('post.id', 0, 'int');
        $content = I('post.content', '', 'trim');
        if (!$content) {
            $this->error('评论内容不能为空！');
        }
        $data = array('content' => $content);
        if ($id) {
            $data['id'] = $id;
            $res        = M('comment')->save($data);
        } else {
            $data['create_time'] = time();
            $res                 = M('comment')->add($data);
        }
        if ($res !== false) {
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
        $res = M('comment')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

}