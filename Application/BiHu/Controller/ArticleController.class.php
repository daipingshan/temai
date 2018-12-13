<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/19
 * Time: 9:31
 */

namespace BiHu\Controller;


class ArticleController extends CommonController {

    /**
     * 文章列表
     */
    public function index() {
        $keyword   = I('get.keyword', '', 'trim');
        $author_id = I('get.author_id', 0, 'int');
        $up_num    = I('get.up_num', 0, 'int');
        $time      = I('get.time', '', 'trim,urldecode');
        $where     = array();
        if ($keyword) {
            $where['article_title|article_content'] = array('like', "%{$keyword}%");
        }
        if ($author_id) {
            $where['author_id'] = $author_id;
        }
        if ($up_num) {
            $where['up_num'] = array('egt', $up_num);
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time);
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
        }
        $count  = M('article')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('article')->where($where)->limit($limit)->order('create_time desc')->select();
        $assign = array(
            'page'   => $page->show(),
            'data'   => $data,
            'author' => S('BiHu_author')
        );
        $this->assign($assign);
        $this->display();
    }
}