<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/4
 * Time: 16:22
 */

namespace Admin\Controller;

/**
 * 文章列表
 * Class NewsController
 *
 * @package Admin\Controller
 */
class NewsController extends CommonController {

    /**
     * 文章列表
     */
    public function index() {
        $title = I('get.title', '', 'trim,urldecode');
        $where = array();
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($this->group_id != 1) {
            switch ($this->user_info['name']) {
                case '程若曦':
                    $where['name'] = array('in', array('徐庆', '王蒙', '杨彦妮', '程若曦'));
                    break;
                case '仰宗虎':
                    break;
                default:
                    $where['name'] = $this->user_info['name'];
            }
        }
        $model  = M('tmnews');
        $count  = $model->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = $model->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 登记文章
     */
    public function add() {
        $this->display('save');
    }

    /**
     * 编辑文章
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('tmnews')->find($id);
        $this->assign('info', $info);
        $this->display('save');
    }

    /**
     * 文章保存
     */
    public function saveNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $title     = I('post.title', '', 'trim');
        $news_type = I('post.newstype', '', 'trim');
        $platform  = I('post.platform', '', 'trim');
        $shen_he   = I('post.shenhe', '', 'trim');
        $ling_yu   = I('post.lingyu', '', 'trim');
        if (empty($title)) {
            $this->error('文章标题不能为空！');
        }
        if (empty($news_type)) {
            $this->error('请选择文章类型！');
        }
        if (empty($platform)) {
            $this->error('请选择写作平台！');
        }
        if (empty($shen_he)) {
            $this->error('请选择审核状态！');
        }
        if (empty($ling_yu)) {
            $this->error('请选择文章领域！');
        }
        $data = array(
            'title'    => $title,
            'newstype' => $news_type,
            'platform' => $platform,
            'shenhe'   => $shen_he,
            'type'     => '放心购文章',
            'lingyu'   => $ling_yu,
        );
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('tmnews')->save($data);
            if ($res !== false) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败！');
            }
        } else {
            $data['name']    = $this->user_info['name'];
            $data['time']    = time();
            $data['user_id'] = $this->user_id;
            $res             = M('tmnews')->add($data);
            if ($res) {
                $this->success('登记成功');
            } else {
                $this->error('登记失败！');
            }
        }
    }

    /**
     * 文章统计
     */
    public function count() {
        $time = I('get.time', '', 'trim,urldecode');
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time);
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-01'));
            $time       = date('Y-m-d', $start_time) . ' ~ ' . date('Y-m-d', $end_time);
        }
        $where     = array('time' => array('between', array($start_time, $end_time)));
        $fxg_where = array('order_status' => array('in', array('订单完成', '订单付款')));
        $field     = 'count(id) as news_num,name,user_id';
        $data      = M('tmnews')->field($field)->where($where)->group('user_id')->index('user_id')->select();
        $user_data = $news_num = $order_num = $total_fee = array();
        foreach ($data as &$val) {
            $news_ids             = M('tmnews')->where($where)->where(array('user_id' => $val['user_id']))->getField('id', true);
            $fxg_where['news_id'] = array('in', array_values($news_ids));
            $fxg                  = M('fxg')->where($fxg_where)->field('count(id) as num,sum(income) as fee')->select();
            $val['order_num']     = $fxg[0]['num'] ? $fxg[0]['num'] : 0;
            $val['order_fee']     = $fxg[0]['fee'] ? $fxg[0]['fee'] : 0;
            $user_data[]          = $val['name'];
            $news_num[]           = $val['news_num'] ? $val['news_num'] : 0;
            $order_num[]          = $fxg[0]['num'] ? $fxg[0]['num'] : 0;
            $total_fee[]          = $fxg[0]['fee'] ? $fxg[0]['fee'] : 0;
        }
        $this->assign(array('data' => $data, 'time' => $time, 'user_data' => json_encode($user_data), 'news_num' => json_encode($news_num), 'order_num' => json_encode($order_num), 'total_fee' => json_encode($total_fee)));
        $this->assign('data', $data);
        $this->assign('time', $time);
        $this->display();
    }

}