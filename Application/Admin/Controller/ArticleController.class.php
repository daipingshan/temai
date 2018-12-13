<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/14
 * Time: 14:05
 */

namespace Admin\Controller;


class ArticleController extends CommonController {

    /**
     * 文章列表
     */
    public function index() {
        $p        = I('get.p', 1, 'int');
        $media_id = I('get.media_id', '', 'trim');
        $where    = array('behot_time' => array('gt', strtotime(date('Y-m-d'))));
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        $temp_data = $data = $time_data = $key_data = $article_id = array();
        if (date('i') > 10) {
            $start_time = strtotime(date('Y-m-d H') . ":00:00") - 7201;
            $end_time   = strtotime(date('Y-m-d H') . ":00:00") + 1;
            $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 7200;
        } else {
            $start_time = strtotime(date('Y-m-d H') . ":00:00") - 10801;
            $end_time   = strtotime(date('Y-m-d H') . ":00:00") - 3599;
            $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 10800;
        }
        while ($temp_time < $end_time) {
            $time_data[] = date('H', $temp_time);
            $temp_time   = $temp_time + 3600;
        }
        $article_data = M('temai_article_read')->field('article_id,max(go_detail_count) as count')->where($where)->group('article_id')->order('count desc')->select();
        foreach ($article_data as $v) {
            $article_id[] = $v['article_id'];
        }
        if (count($article_id) > 0) {
            $article_id_chunk = array_chunk($article_id, 50);
            $count            = count($article_id);
            $page             = $this->pages($count, 50);
            $where            = array('article_id' => array('in', $article_id_chunk[$p - 1]), 'create_time' => array('between', array($start_time, $end_time)));
            $db_data          = M('temai_article_read')->where($where)->order('go_detail_count desc')->select();
            foreach ($db_data as $val) {
                $time                                        = date('H', $val['create_time']);
                $temp_data[$val['article_id']]['article_id'] = $val['article_id'];
                $temp_data[$val['article_id']]['send_time']  = date('Y-m-d H:i:s', $val['behot_time']);
                $temp_data[$val['article_id']]['title']      = $val['title'];
                $temp_data[$val['article_id']]['type_name']  = C('ARTICLE_CATE')[$val['media_id']];
                $temp_data[$val['article_id']]['is_refresh'] = in_array($val['create_user_id'], $this->create_user_id) ? 1 : 0;
                $temp_data[$val['article_id']][$time]        = $val['go_detail_count'];
            }
            foreach ($temp_data as $key => $val) {
                $data[$key] = array('article_id' => $val['article_id'], 'title' => $val['title'], 'type_name' => $val['type_name'], 'send_time' => $val['send_time'], 'is_refresh' => $val['is_refresh']);
                foreach ($time_data as $v) {
                    $data[$key][$v . ':00'] = $val[$v] ? : 0;
                }
            }
            foreach ($time_data as $val) {
                $key_data[] = $val . ":00";
            }
            $this->assign('page', $page->show());
        }
        $this->assign(array('data' => $data, 'key_data' => $key_data, 'media_id' => C('ARTICLE_CATE'), 'type' => 'one'));
        $this->display();
    }

    /**
     * 文章列表
     */
    public function articleList() {
        $p           = I('get.p', 1, 'int');
        $media_id    = I('get.media_id', '', 'trim');
        $send_time   = I('get.send_time', '', 'trim,urldecode');
        $create_time = I('get.create_time', '', 'trim,urldecode');
        $title       = I('get.title', '', 'trim');
        $news_id     = I('get.article_id', '', 'trim');
        $max_time    = 12 * 3600;
        $min_time    = 3600;
        $temp_time   = 0;
        $error_info  = null;
        if ($send_time) {
            list($start_time, $end_time) = explode(' ~ ', $send_time);
            $start_time          = strtotime($start_time);
            $end_time            = strtotime($end_time);
            $where['behot_time'] = array('between', array($start_time, $end_time));
        } else {
            $where = array('behot_time' => array('gt', strtotime(date('Y-m-d'))));
        }
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        $temp_data = $data = $time_data = $key_data = $article_id = array();
        if ($create_time) {
            list($start_time, $end_time) = explode(' ~ ', $create_time);
            $start_time = strtotime($start_time);
            $end_time   = strtotime($end_time);
            if ($end_time - $start_time > $max_time) {
                $error_info = "文章采集时间不能超过12小时，请重新筛选！";
            } else if ($end_time - $start_time < $min_time) {
                $error_info = "文章采集时间不能少于1小时，请重新筛选！";
            } else {
                $start_time = strtotime(date('Y-m-d H', $start_time) . ":00:00") - 1;
                $end_time   = strtotime(date('Y-m-d H', $end_time) . ":00:00") + 1;
                $temp_time  = strtotime(date('Y-m-d H', $start_time + 1) . ":00:00");
            }
        } else {
            if (date('i') > 10) {
                $start_time = strtotime(date('Y-m-d H') . ":00:00") - 7201;
                $end_time   = strtotime(date('Y-m-d H') . ":00:00") + 1;
                $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 7200;
            } else {
                $start_time = strtotime(date('Y-m-d H') . ":00:00") - 10801;
                $end_time   = strtotime(date('Y-m-d H') . ":00:00") - 3599;
                $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 10800;
            }
        }
        $where['create_time'] = array('between', array($start_time, $end_time));
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($news_id) {
            $where['article_id'] = $news_id;
        }
        if (empty($error_info)) {
            while ($temp_time < $end_time) {
                $time_data[] = date('H', $temp_time);
                $temp_time   = $temp_time + 3600;
            }
            $article_data = M('temai_article_read')->field('article_id,max(go_detail_count) as count')->where($where)->group('article_id')->order('count desc')->select();
            foreach ($article_data as $v) {
                $article_id[] = $v['article_id'];
            }
            $article_id_chunk = array_chunk($article_id, 50);
            if (count($article_id) > 0 && isset($article_id_chunk[$p - 1])) {
                $count   = count($article_id);
                $page    = $this->pages($count, 50);
                $where   = array('article_id' => array('in', $article_id_chunk[$p - 1]));
                $db_data = M('temai_article_read')->where($where)->order('go_detail_count desc')->select();
                foreach ($db_data as $val) {
                    $time                                        = date('H', $val['create_time']);
                    $temp_data[$val['article_id']]['article_id'] = $val['article_id'];
                    $temp_data[$val['article_id']]['send_time']  = date('Y-m-d H:i:s', $val['behot_time']);
                    $temp_data[$val['article_id']]['title']      = $val['title'];
                    $temp_data[$val['article_id']]['type_name']  = C('ARTICLE_CATE')[$val['media_id']];
                    $temp_data[$val['article_id']]['is_refresh'] = in_array($val['create_user_id'], $this->create_user_id) ? 1 : 0;
                    $temp_data[$val['article_id']][$time]        = $val['go_detail_count'];
                }
                foreach ($temp_data as $key => $val) {
                    $data[$key] = array('article_id' => $val['article_id'], 'title' => $val['title'], 'type_name' => $val['type_name'], 'send_time' => $val['send_time'], 'is_refresh' => $val['is_refresh']);
                    foreach ($time_data as $v) {
                        $data[$key][$v . ':00'] = $val[$v] ? : 0;
                    }
                }
                foreach ($time_data as $val) {
                    $key_data[] = $val . ":00";
                }
                $this->assign('page', $page->show());
            }
        }

        $this->assign(array('data' => $data, 'key_data' => $key_data, 'media_id' => C('ARTICLE_CATE'), 'type' => 'two', 'error_info' => $error_info));
        $this->display('index');
    }

    /**
     * 文章列表
     */
    public function numList() {
        $p           = I('get.p', 1, 'int');
        $media_id    = I('get.media_id', '', 'trim');
        $send_time   = I('get.send_time', '', 'trim,urldecode');
        $create_time = I('get.create_time', '', 'trim,urldecode');
        $title       = I('get.title', '', 'trim');
        $news_id     = I('get.article_id', '', 'trim');
        $max_time    = 12 * 3600;
        $min_time    = 3600;
        $temp_time   = 0;
        $error_info  = null;
        if ($send_time) {
            list($start_time, $end_time) = explode(' ~ ', $send_time);
            $start_time          = strtotime($start_time);
            $end_time            = strtotime($end_time);
            $where['behot_time'] = array('between', array($start_time, $end_time));
        } else {
            $where = array('behot_time' => array('gt', strtotime(date('Y-m-d'))));
        }
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        $temp_data = $data = $time_data = $key_data = $article_id = array();
        if ($create_time) {
            list($start_time, $end_time) = explode(' ~ ', $create_time);
            $start_time = strtotime($start_time);
            $end_time   = strtotime($end_time);
            if ($end_time - $start_time > $max_time) {
                $error_info = "文章采集时间不能超过12小时，请重新筛选！";
            } else if ($end_time - $start_time < $min_time) {
                $error_info = "文章采集时间不能少于1小时，请重新筛选！";
            } else {
                $start_time = strtotime(date('Y-m-d H', $start_time) . ":00:00") - 1;
                $end_time   = strtotime(date('Y-m-d H', $end_time) . ":00:00") + 1;
                $temp_time  = strtotime(date('Y-m-d H', $start_time + 1) . ":00:00");
            }
        } else {
            if (date('i') > 10) {
                $start_time = strtotime(date('Y-m-d H') . ":00:00") - 7201;
                $end_time   = strtotime(date('Y-m-d H') . ":00:00") + 1;
                $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 7200;
            } else {
                $start_time = strtotime(date('Y-m-d H') . ":00:00") - 10801;
                $end_time   = strtotime(date('Y-m-d H') . ":00:00") - 3599;
                $temp_time  = strtotime(date('Y-m-d H') . ":00:00") - 10800;
            }
        }
        $where['create_time'] = array('between', array($start_time, $end_time));
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($news_id) {
            $where['article_id'] = $news_id;
        }
        if (empty($error_info)) {
            while ($temp_time < $end_time) {
                $time_data[] = date('H', $temp_time);
                $temp_time   = $temp_time + 3600;
            }
            $article_data = M('temai_article_read')->field('article_id,max(num) as num')->where($where)->group('article_id')->order('num desc')->select();
            foreach ($article_data as $v) {
                $article_id[] = $v['article_id'];
            }
            $article_id_chunk = array_chunk($article_id, 50);
            if (count($article_id) > 0 && isset($article_id_chunk[$p - 1])) {
                $count   = count($article_id);
                $page    = $this->pages($count, 50);
                $where   = array('article_id' => array('in', $article_id_chunk[$p - 1]));
                $db_data = M('temai_article_read')->where($where)->order('num desc')->select();
                foreach ($db_data as $val) {
                    $time                                        = date('H', $val['create_time']);
                    $temp_data[$val['article_id']]['article_id'] = $val['article_id'];
                    $temp_data[$val['article_id']]['send_time']  = date('Y-m-d H:i:s', $val['behot_time']);
                    $temp_data[$val['article_id']]['title']      = $val['title'];
                    $temp_data[$val['article_id']]['type_name']  = C('ARTICLE_CATE')[$val['media_id']];
                    $temp_data[$val['article_id']]['is_refresh'] = in_array($val['create_user_id'], $this->create_user_id) ? 1 : 0;
                    $temp_data[$val['article_id']][$time]        = $val['num'];
                }
                foreach ($temp_data as $key => $val) {
                    $data[$key] = array('article_id' => $val['article_id'], 'title' => $val['title'], 'type_name' => $val['type_name'], 'send_time' => $val['send_time'], 'is_refresh' => $val['is_refresh']);
                    foreach ($time_data as $v) {
                        $data[$key][$v . ':00'] = $val[$v] ? : 0;
                    }
                }
                foreach ($time_data as $val) {
                    $key_data[] = $val . ":00";
                }
                $this->assign('page', $page->show());
            }
        }

        $this->assign(array('data' => $data, 'key_data' => $key_data, 'media_id' => C('ARTICLE_CATE'), 'type' => 'three', 'error_info' => $error_info));
        $this->display('index');
    }

    /**
     * 文章走势
     */
    public function detail() {
        $article_id = I('get.article_id', '', 'trim');
        $data       = M('temai_article_read')->where(array('article_id' => $article_id))->select();
        $title      = '文章标题';
        $count_data = $num_data = $time_data = array();
        foreach ($data as $val) {
            $count_data[] = $val['go_detail_count'];
            $num_data[]   = $val['num'];
            $time_data[]  = date('Y-m-d H', $val['create_time']) . ":00";
            $title        = $val['title'];
        }
        $this->assign(array('time_data' => json_encode($time_data), 'count_data' => json_encode($count_data), 'num_data' => json_encode($num_data), 'title' => $title));
        $this->display();
    }

    /**
     * 根据时间查看
     */
    public function articleInfo() {
        $time = I('get.time', '', 'trim');
        if (empty($time)) {
            $time = date('Y-m-d', strtotime('-1 days'));
        }
        $data = $count_data = $max_data = array();
        for ($i = 0; $i < 24; $i++) {
            if ($i < 10) {
                $data["0" . $i . ":00"]['count'] = 0;
                $data["0" . $i . ":00"]['max']   = 0;
            } else {
                $data[$i . ":00"]['count'] = 0;
                $data[$i . ":00"]['max']   = 0;
            }
        }
        $start_time   = strtotime($time);
        $end_time     = $start_time + 86400;
        $where        = array('behot_time' => array('between', array($start_time, $end_time)));
        $article_data = M('temai_article')->field('id,article_id,go_detail_count,FROM_UNIXTIME(behot_time,"%H") as time')->where($where)->group('article_id')->order('go_detail_count desc')->select();
        foreach ($article_data as $val) {
            $data[$val['time'] . ":00"]['count'] = $data[$val['time'] . ":00"]['count'] + 1;
            if ($val['go_detail_count'] > 10000) {
                $data[$val['time'] . ":00"]['max'] = $data[$val['time'] . ":00"]['max'] + 1;
            }
        }
        $time_data = array_keys($data);
        foreach ($data as $val) {
            $max_data[]   = $val['max'];
            $count_data[] = $val['count'];
        }
        $this->assign(array('time' => $time, 'data' => $data, 'time_data' => json_encode($time_data), 'max_data' => json_encode($max_data), 'count_data' => json_encode($count_data)));
        $this->display();
    }

    /**
     * 文章详情
     */
    public function articleDetail() {
        $p    = I('get.p', 1, 'int');
        $time = I('get.time', '', 'trim');
        $h    = I('get.h', '', 'urldecode,trim');
        $type = I('get.type', 'all', 'trim');
        if (empty($time) || empty($h)) {
            $this->assign('error_info', '请求参数不合法！');
        } else {
            $data = $time_data = array();
            $hour = intval($h);
            for ($i = $hour + 1; $i < 24; $i++) {
                if ($i < 10) {
                    $time_data[] = '0' . $i . ":00";
                } else {
                    $time_data[] = $i . ":00";
                }
            }
            $start_time = strtotime($time . " " . $h . ":00");
            $end_time   = $start_time + 3600;
            $where      = array('behot_time' => array('between', array($start_time, $end_time)));
            if ($type == 'max') {
                $where['go_detail_count'] = array('egt', 10000);
            }
            $article_data     = M('temai_article_read')->index('article_id')->where($where)->field('article_id,max(go_detail_count) as go_detail_count')->group('article_id')->order('go_detail_count desc')->select();
            $article_id       = array_keys($article_data);
            $article_id_chunk = array_chunk($article_id, 50);
            $count            = count($article_id);
            $page             = $this->pages($count, 50);
            $where            = array('article_id' => array('in', $article_id_chunk[$p - 1]));
            $db_data          = M('temai_article_read')->field('*,FROM_UNIXTIME(create_time,"%H:%i") as time')->where($where)->select();
            foreach ($db_data as $val) {
                $data[$val['article_id']]['behot_time'] = date("Y-m-d H:i:s", $val['behot_time']);
                $data[$val['article_id']]['article_id'] = $val['article_id'];
                $data[$val['article_id']]['title']      = $val['title'];
                $data[$val['article_id']][$val['time']] = $val['go_detail_count'];
            }
            foreach ($data as &$row) {
                foreach ($time_data as $t) {
                    if (!isset($row[$t])) {
                        $row[$t] = 0;
                    }
                }
            }
            $this->assign(array('time_data' => $time_data, 'data' => $data, 'page' => $page->show()));
        }
        $this->display();
    }

    /**
     * 藏金阁文章
     */
    public function taoJinGeArticle() {
        $where = array();
        $title = I('get.title', '', 'trim');
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        $count = M('tao_jin_ge_article')->where($where)->count();
        $page  = $this->pages($count, 20);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('tao_jin_ge_article')->where($where)->limit($limit)->order('add_time desc')->select();
        foreach ($data as &$val) {
            if (strpos($val['article_url'], 'www.toutiao.com')) {
                $val['is_top'] = 1;
            } else {
                $val['is_top'] = 2;
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
     * 选择发文账号
     */
    public function selectAccount() {
        $id      = I('get.id', 0, 'int');
        $account = $this->_getTopLineAccount();
        $this->assign('account', $account);
        $this->assign('id', $id);
        $this->display();

    }

    /**
     * 藏金阁文章详情
     */
    public function taoJinGeArticleInfo() {
        $id   = I('get.id', 0, 'int');
        $info = M('tao_jin_ge_article')->find($id);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 一键发文
     */
    public function sendArticle() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id         = I('post.id', 0, 'int');
        $account_id = I('post.account_id', 0, 'int');
        $info       = M('tao_jin_ge_article')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('文章信息不存在！');
        }
        if (empty($info['send_content'])) {
            $this->error('文章内容尚未处理完成，请发送已处理完成的数据！');
        }
        $res = $this->_sendTop($info['title'], $info['send_content'], $info['covers'], $account_id);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        } else {
            $res = json_decode($res, true);
            if ($res['code'] == 0 && trim($res['message']) != 'error') {
                M('tao_jin_ge_article')->save(array('id' => $info['id'], 'is_send' => 1));
                $this->success($res['message']);
            } else {
                $this->error($res['message']);
            }
        }


    }


    /**
     * @param $title
     * @param $content
     * @param $covers
     * @param int $account_id
     * @return bool|mixed
     */
    protected function _sendTop($title, $content, $covers, $account_id = 24) {
        $post_data = array(
            'article_type'           => 0,
            'title'                  => $title,
            'content'                => $content,
            'activity_tag'           => 0,
            'title_id'               => '',
            'claim_origin'           => 0,
            'article_ad_type'        => 3,
            'add_third_title'        => 0,
            'recommend_auto_analyse' => 0,
            'tag'                    => 'news',
            'article_label'          => '',
            'is_fans_article'        => 0,
            'govern_forward'         => 0,
            'push_status'            => 0,
            'push_android_title'     => '',
            'push_android_summary'   => '',
            'push_ios_summary'       => '',
            'timer_status'           => 0,
            'timer_time'             => date('Y-m-d H:i'),
            'column_chosen'          => 0,
            'pgc_id'                 => '',
            'pgc_feed_covers'        => $covers,
            'need_pay'               => 0,
            'from_diagnosis'         => 0,
            'save'                   => 0,
        );
        $account   = $this->_getTopLineAccount();
        $res       = $this->_post("https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=figure", $post_data, $account[$account_id]['cookie']);
        return $res;
    }

}