<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/13
 * Time: 16:29
 */

namespace Fxg\Controller;


class ShopController extends CommonController {

    /**
     * 商品列表
     */
    public function index() {
        if (IS_AJAX) {
            $shop_name = I('get.shop_name', '', 'trim');
            $page      = I('get.page', 1, 'int');
            $limit     = I('get.limit', 10, 'int');
            $where     = array();
            if (!empty($username)) {
                $where['shop_name'] = $shop_name;
            }
            $model = M('shop');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d', $val['create_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 添加店铺
     */
    public function add() {
        $this->display('save');
    }

    /**
     * 修改店铺
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('shop')->find($id);
        $this->assign('info', $info);
        $this->display('save');
    }

    /**
     * 保存供货商信息
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id         = I('post.id', 0, 'int');
        $shop_name  = I('post.shop_name', '', 'trim');
        $app_key    = I('post.app_key', '', 'trim');
        $app_secret = I('post.app_secret', '', 'trim');
        if (empty($shop_name)) {
            $this->error('店铺名称不能为空！');
        }
        if (empty($app_key)) {
            $this->error('app_key不能为空！');
        }
        if (empty($app_secret)) {
            $this->error('app_secret不能为空！');
        }
        $data = array('shop_name' => $shop_name, 'app_key' => $app_key, 'app_secret' => $app_secret);
        if ($id > 0) {
            $res = M('shop')->where(array('id' => $id))->save($data);
        } else {
            $data['create_time'] = time();
            $res                 = M('shop')->add($data);
        }
        if ($res !== false) {
            S('shop', null);
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}