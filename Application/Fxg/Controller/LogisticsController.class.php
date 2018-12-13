<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/11/3
 * Time: 11:26
 */

namespace Fxg\Controller;

/**
 * Class LogisticsController
 *
 * @package Fxg\Controller
 */
class LogisticsController extends CommonController {

    /**
     * 快递公司列表
     */
    public function index() {
        if (IS_AJAX) {
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $where = array();
            $model = M('logistics_list');
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 添加快递
     */
    public function add() {
        $this->assign('type', 'add');
        $this->display('save');
    }

    /**
     * 保存快递
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id           = I('post.id', 0, 'int');
        $name         = I('post.name', '', 'trim');
        $english_name = I('post.english_name', '', 'trim');
        if (!$name) {
            $this->error('快递名称不能为空！');
        }
        if (!$english_name) {
            $this->error('快递拼音不能为空！');
        }
        $logistics_id = M('logistics')->where(array('name' => $name))->getField('logistics_id');
        if (empty($logistics_id)) {
            $this->error('快递公司名称不存在快递列表中！');
        }
        $data = array('logistics_id' => $logistics_id, 'name' => $name, 'english_name' => $english_name);
        if ($id > 0) {
            $res = M('logistics_list')->where(array('id' => $id))->save($data);
        } else {
            $res = M('logistics_list')->add($data);
        }
        if ($res !== false) {
            S('logistics_list', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 修改快递
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('logistics_list')->find($id);
        $this->assign('info', $info);
        $this->assign('type', 'update');
        $this->display('save');
    }


}