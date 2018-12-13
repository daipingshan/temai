<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/24
 * Time: 9:35
 */

namespace BiHu\Controller;

/**
 * Class ArticleLogController
 *
 * @package BiHu\Controller
 */
class ArticleLogController extends CommonController {

    /**
     * 日志记录
     */
    public function index() {
        $keyword      = I('get.keyword', '', 'trim');
        $account_name = I('get.account_name', '', 'trim');
        $time         = I('get.time', '', 'trim,urldecode');
        $where        = array();
        if ($keyword) {
            $where['title'] = array('like', "%{$keyword}%");
        }
        if ($account_name) {
            $where['account_name'] = array('like', "%{$account_name}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time);
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
        }
        $count  = M('article_up_log')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('article_up_log')->where($where)->limit($limit)->order('create_time desc')->select();
        $assign = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }
}