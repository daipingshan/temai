<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/10
 * Time: 8:44
 */

namespace Admin\Controller;

/**
 * Class SaleArticleController
 *
 * @package Admin\Controller
 */
class SaleArticleController extends CommonController {

    /**
     * 文章管理
     */
    public function index() {
        $time           = I('get.time', '', 'trim,urldecode');
        $media_id       = I('get.media_id', '', 'trim');
        $title          = I('get.keyword', '', 'trim');
        $read_num       = I('get.read_num', 0, 'int');
        $create_user_id = I('get.create_user_id', 0, 'int');
        $sort           = I('get.sort', '', 'int');
        $article_genre  = I('get.article_genre', 0, 'int');
        $where          = array();
        $order          = 'behot_time desc';
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($read_num) {
            $where['go_detail_count'] = array('egt', $read_num);
        }
        if ($create_user_id) {
            $where['create_user_id'] = $create_user_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time          = strtotime($start_time);
                $end_time            = strtotime($end_time);
                $where['behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($sort) {
            if ($sort == 1) {
                $order = 'go_detail_count desc,behot_time desc';
            } elseif ($sort == 2) {
                $order = 'comments_count desc,behot_time desc';
            }
        }
        if ($article_genre) {
            $where['article_genre'] = $article_genre;
        }
        $count  = M('temai_article')->where($where)->count('id');
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('temai_article')->where($where)->order($order)->limit($limit)->select();
        $author = $this->author;
        $userid = array_keys($author);
        if (count($data) > 0) {
            $author_id = array();
            foreach ($data as &$val) {
                $author_id[] = $val['create_user_id'];
                if (in_array($val['create_user_id'], $userid)) {
                    $val['userid'] = '1';
                } else {
                    $val['userid'] = '0';
                }
            }
            $author_data = M('temai_article')->field('count(id) as num,create_user_id')->where(array('create_user_id' => array('in', $author_id)))->where($where)->index('create_user_id')->group('create_user_id')->select();
            foreach ($data as &$val) {
                $val['num'] = $author_data[$val['create_user_id']]['num'];
            }
        }
        $assign = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 文章统计
     */
    public function count() {
        $time           = I('get.time', '', 'trim,urldecode');
        $media_id       = I('get.media_id', '', 'trim');
        $title          = I('get.keyword', '', 'trim');
        $create_user_id = I('get.create_user_id', 0, 'int');
        $read_num       = I('get.read_num', '', 'int');
        $sort           = I('get.sort', '', 'int');
        $article_genre  = I('get.article_genre', 0, 'int');
        $where          = array();
        $order          = 'behot_time desc';
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($create_user_id) {
            $where['create_user_id'] = $create_user_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time          = strtotime($start_time);
                $end_time            = strtotime($end_time);
                $where['behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($read_num) {
            $where['go_detail_count'] = array('egt', $read_num);
        }
        if ($sort) {
            if ($sort == 1) {
                $order = 'go_detail_count desc,behot_time desc';
            } elseif ($sort == 2) {
                $order = 'comments_count desc,behot_time desc';
            }
        }
        if ($article_genre) {
            $where['article_genre'] = $article_genre;
        }
        $count = M('temai_article')->where($where)->count('id');
        $page  = $this->pages($count, $this->limit);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('temai_article')->where($where)->order($order)->limit($limit)->select();
        if (count($data) > 0) {
            $author_id = array();
            foreach ($data as $val) {
                $author_id[] = $val['create_user_id'];
            }
            $author_data = M('temai_article')->field('count(id) as num,create_user_id')->where(array('create_user_id' => array('in', $author_id)))->where($where)->index('create_user_id')->group('create_user_id')->select();
            foreach ($data as &$val) {
                $val['num'] = $author_data[$val['create_user_id']]['num'];
            }
        }
        $this->_getArticleCount($where);
        $assign = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * @param $where
     */
    protected function _getArticleCount($where) {
        $where_count = array('go_detail_count' => array('egt', 10000));
        if (empty($where['behot_time'])) {
            $start_time          = strtotime(date('Y-m-d', strtotime('-7 days')));
            $end_time            = strtotime(date('Y-m-d')) + 86399;
            $where['behot_time'] = array('between', array($start_time, $end_time));
        }
        $db_data_count = M('temai_article')->field('count(id) as total,FROM_UNIXTIME(behot_time,"%Y-%m-%d") as time')->where($where)->group('time')->select();
        $db_data       = M('temai_article')->field('go_detail_count,FROM_UNIXTIME(behot_time,"%Y-%m-%d") as time')->where($where)->where($where_count)->select();
        $data          = $_data = array();
        foreach ($db_data_count as $val) {
            $data['time_data'][]               = $val['time'];
            $data['total_num'][]               = $val['total'];
            $data['w_num'][$val['time']]       = 0;
            $data['one_w_num'][$val['time']]   = 0;
            $data['two_w_num'][$val['time']]   = 0;
            $data['three_w_num'][$val['time']] = 0;
            $data['four_w_num'][$val['time']]  = 0;
            $data['five_w_num'][$val['time']]  = 0;
            $data['six_w_num'][$val['time']]   = 0;
            $data['seven_w_num'][$val['time']] = 0;
            $data['eight_w_num'][$val['time']] = 0;
            $data['nine_w_num'][$val['time']]  = 0;
            $data['ten_w_num'][$val['time']]   = 0;
        }
        foreach ($db_data as $val) {
            $_data[$val['time']][] = $val;
        }
        foreach ($_data as $key => $row) {
            foreach ($row as $val) {
                if ($val['go_detail_count'] >= 1000000) {
                    $data['ten_w_num'][$key] = $data['ten_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 900000) {
                    $data['nine_w_num'][$key] = $data['nine_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 800000) {
                    $data['eight_w_num'][$key] = $data['eight_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 700000) {
                    $data['seven_w_num'][$key] = $data['seven_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 600000) {
                    $data['six_w_num'][$key] = $data['six_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 500000) {
                    $data['five_w_num'][$key] = $data['five_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 400000) {
                    $data['four_w_num'][$key] = $data['four_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 300000) {
                    $data['three_w_num'][$key] = $data['three_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 200000) {
                    $data['two_w_num'][$key] = $data['two_w_num'][$key] + 1;
                } else if ($val['go_detail_count'] >= 100000) {
                    $data['one_w_num'][$key] = $data['one_w_num'][$key] + 1;
                } else {
                    $data['w_num'][$key] = $data['w_num'][$key] + 1;
                }
            }
        }
        foreach ($data as $key => $val) {
            $this->assign($key, json_encode(array_values($val)));
        }
    }

    /**
     * 我的文章
     */
    public function userArticle() {
        $time           = I('get.time', '', 'trim,urldecode');
        $media_id       = I('get.media_id', '', 'trim');
        $title          = I('get.keyword', '', 'trim');
        $create_user_id = I('get.create_user_id', '', 'int');
        $read_num       = I('get.read_num', '', 'int');
        $sort           = I('get.sort', '', 'int');
        $order          = 'behot_time desc';
        $author         = $this->author;
        $where          = array('create_user_id' => array('in', array_keys($author)));
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($create_user_id) {
            $where['create_user_id'] = $create_user_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time          = strtotime($start_time);
                $end_time            = strtotime($end_time);
                $where['behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($read_num) {
            $where['go_detail_count'] = array('egt', $read_num);
        }
        if ($sort) {
            if ($sort == 1) {
                $order = 'go_detail_count desc,behot_time desc';
            } elseif ($sort == 2) {
                $order = 'comments_count desc,behot_time desc';
            }
        }
        $count     = M('temai_article')->where($where)->count('id');
        $page      = $this->pages($count, $this->limit);
        $limit     = $page->firstRow . ',' . $page->listRows;
        $data      = M('temai_article')->where($where)->order($order)->limit($limit)->select();
        $author_id = array();
        foreach ($data as $val) {
            $author_id[] = $val['create_user_id'];
        }
        $author_data = M('temai_article')->field('count(id) as num,create_user_id')->where(array('create_user_id' => array('in', $author_id)))->where($where)->index('create_user_id')->group('create_user_id')->select();
        foreach ($data as &$val) {
            $val['num'] = $author_data[$val['create_user_id']]['num'];
        }
        $this->_getArticleCount($where);
        $assign = array(
            'page'   => $page->show(),
            'data'   => $data,
            'author' => $author,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 历史文章数据
     */
    public function historyArticle() {
        $week   = I('get.week', 0, 'int');
        $time   = I('get.time', '', 'trim,urldecode');
        $submit = I('get.submit', 1, 'int');
        if ($week && $time) {
            $date_data = array();
            list($start_time, $end_time) = explode(' ~ ', $time);
            $date_status = true;
            $time        = $start_time;
            while ($date_status == true) {
                if (strtotime($time) > strtotime($end_time)) {
                    $date_status = false;
                } else {
                    $temp_week = date("w", strtotime($time));
                    $temp_week = $temp_week == 0 ? 7 : $temp_week;
                    if ($temp_week == $week) {
                        if ($submit == 2) {
                            if (count($date_data) < 4) {
                                $date_data[] = $time;
                            }
                        } else if ($submit == 1) {
                            $date_data[] = $time;
                        }
                    }
                    $time = date('Y-m-d', strtotime("{$time} +1 days"));
                }
            }
            if ($date_data) {
                if ($submit == 1) {
                    $where  = array('FROM_UNIXTIME(behot_time,"%Y-%m-%d")' => array('in', $date_data), 'go_detail_count' => array('gt', 10000));
                    $count  = M('temai_article')->where($where)->count('id');
                    $page   = $this->pages($count, $this->limit);
                    $limit  = $page->firstRow . ',' . $page->listRows;
                    $data   = M('temai_article')->where($where)->order('go_detail_count desc')->limit($limit)->select();
                    $assign = array(
                        'page' => $page->show(),
                        'data' => $data,
                    );
                    $this->assign($assign);
                } else {
                    foreach ($date_data as $key => $val) {
                        $data[$key]['time'] = $val;
                        $data[$key]['data'] = M('temai_article')->where(array('FROM_UNIXTIME(behot_time,"%Y-%m-%d")' => $val, 'go_detail_count' => array('gt', 10000)))->order('go_detail_count desc,behot_time desc')->select();
                    }
                    $assign = array(
                        'data' => $data,
                    );
                    $this->assign($assign);
                }

            }
        }
        $this->display();
    }


}