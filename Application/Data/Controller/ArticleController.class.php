<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/5
 * Time: 9:57
 */

namespace Data\Controller;

use Common\Org\Http;

/**
 * 文章采集器
 * Class ArticleController
 *
 * @package Data\Controller
 */
class ArticleController extends CommonController {


    /**
     * 发文地址
     *
     * @var string
     */
    private $send_url = "https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=figure";

    /**
     * 上传图片地址
     *
     * @var string
     */
    private $upload_url = "https://mp.toutiao.com/tools/upload_picture/?type=ueditor&pgc_watermark=1&action=uploadimage&encode=utf-8";

    /**
     * @var array
     */
    protected $media_id = array(6768458493, 5500903267, 5500358214, 5500311915, 5500950648);

    /**
     * @var array
     */
    protected $cateArr = array(5500950648, 5500838410, 5500803015, 5500367585, 5501814762, 5501679303, 5500311915, 5500358214, 5500903267, 5501832587, 6768458493);

    /**
     * 添加文章数据
     */
    public function addArticle() {
        $media_id = I('get.media_id', '', 'trim');
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-开始添加请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            exit;
        }
        $start_time = time();
        $end_time   = time() - 2400;
        $get_num    = 0;
        $temp_time  = $start_time;
        $data       = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 100,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $proxy     = $this->_getProxy($media_id);
            $res       = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id = intval(get_word($val['article_url'], "\?id=", '&'));
                    if ($news_id > 0) {
                        $data[$news_id] = array(
                            'news_id'         => $news_id,
                            'article_id'      => $val['str_group_id'],
                            'title'           => $val['title'],
                            'comments_count'  => $val['comment_count'],
                            'go_detail_count' => $val['total_read_count'],
                            'behot_time'      => strtotime($val['datetime']),
                            'url'             => $val['article_url'],
                            'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                            'media_id'        => $media_id,
                            'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                        );
                        $news_ids[]     = $news_id;
                    }
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            //dump($data);exit;
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs('add_temai_article_error.log', array('param' => $param, 'msg' => $msg));
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部采集完成";
            $this->_addLogs('add_temai_article_success.log', $msg);
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $date = date('Y-m-d H:i');
            $msg  = "分类ID:{$media_id}，{$date}采集成功";
            $this->_addLogs('add_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_add_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-结束添加请求");
    }

    /**
     * 添加文章数据
     */
    public function addTempArticle() {
        $media_id = I('get.media_id', '', 'trim');
        $this->_addLogs("add_temai_article_msg_temp_{$media_id}.log", "开始添加请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("add_temai_article_msg_{$media_id}.log", $msg);
            exit;
        }
        $start_time = I('get.start_time', '', 'trim');
        $end_time   = I('get.end_time', '', 'trim');
        $get_num    = 0;
        if (!$start_time) {
            $start_time = time();
        } else {
            $start_time = strtotime($start_time);
        }
        $end_time  = strtotime($end_time);
        $temp_time = $start_time;
        $data      = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 10,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $proxy     = $this->_getProxy($media_id);
            $res       = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id = intval(get_word($val['article_url'], "\?id=", '&'));
                    if ($news_id > 0) {
                        $data[$news_id] = array(
                            'news_id'         => $news_id,
                            'article_id'      => $val['str_group_id'],
                            'title'           => $val['title'],
                            'comments_count'  => $val['comment_count'],
                            'go_detail_count' => $val['total_read_count'],
                            'behot_time'      => strtotime($val['datetime']),
                            'url'             => $val['article_url'],
                            'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                            'media_id'        => $media_id,
                            'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                        );
                        $news_ids[]     = $news_id;
                    }
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('Y-m-d H:i', $start_time == 0 ? time() : $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("add_temai_article_msg_temp_{$media_id}.log", $msg);
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs('add_temai_article_error.log', array('param' => $param, 'msg' => $msg));
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部采集完成";
            $this->_addLogs('add_temai_article_success.log', $msg);
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $date = date('Y-m-d H:i');
            $msg  = "分类ID:{$media_id}，{$date}采集成功";
            $this->_addLogs('add_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_add_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("add_temai_article_msg_{$media_id}.log", date('Y-m-d H:i:s') . "-结束添加请求");
    }

    /**
     * 更新当天数据
     */
    public function updateDayArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d H") . ":00:00";
        $end_time   = date("Y-m-d") . " 00:00:00";
        $this->_updateArticle($media_id, $start_time, $end_time, 'day');
    }

    /**
     * 更新昨天前天数据
     */
    public function updateTwoArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d");
        $end_time   = date('Y-m-d', strtotime("-2 days"));
        $this->_updateArticle($media_id, $start_time, $end_time, 'two');
    }

    /**
     * 更新昨天前天数据
     */
    public function updateFourArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date('Y-m-d', strtotime('-2 days'));
        $end_time   = date('Y-m-d', strtotime('-7 days'));
        $this->_updateArticle($media_id, $start_time, $end_time, 'four');
    }

    /**
     * 添加当天阅读量文章
     */
    public function addDayArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d H:i:s");
        $end_time   = date('Y-m-d');
        $this->_addArticle($media_id, $start_time, $end_time, 'day');
    }

    /**
     * 添加当天阅读量文章
     */
    public function addYesTerDayArticle() {
        $media_id   = I('get.media_id', '', 'trim');
        $start_time = date("Y-m-d");
        $end_time   = date('Y-m-d', strtotime("-1 days"));
        $this->_addArticle($media_id, $start_time, $end_time, 'yesterday');
    }

    /**
     * 更新文章数据
     *
     * @param $media_id
     * @param $start_time
     * @param $end_time
     * @param $type
     */
    public function _updateArticle($media_id, $start_time, $end_time, $type) {
        $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-开始更新{$start_time}-{$end_time}请求");
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            exit;
        }

        if (!$start_time || !$end_time) {
            $msg = "时间不能为空";
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            exit;
        }
        $get_num    = 0;
        $start_time = strtotime($start_time);
        $end_time   = strtotime($end_time);
        $temp_time  = $start_time;
        $data       = $add_data = $new_ids_data = $news_ids = array();
        while ($end_time < $temp_time) {
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 20,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $proxy     = $this->_getProxy($media_id);
            $res       = json_decode($this->_get($url, $param, $proxy), true);
            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                foreach ($res['data'] as $val) {
                    $news_id = intval(get_word($val['article_url'], "\?id=", '&'));
                    if ($news_id > 0) {
                        $data[$news_id] = array(
                            'news_id'         => $news_id,
                            'article_id'      => $val['str_group_id'],
                            'title'           => $val['title'],
                            'comments_count'  => $val['comment_count'],
                            'go_detail_count' => $val['total_read_count'],
                            'behot_time'      => strtotime($val['datetime']),
                            'url'             => $val['article_url'],
                            'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                            'media_id'        => $media_id,
                            'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                        );
                        $news_ids[]     = $news_id;
                    }
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('m-d H:i') . '数据获取start_time：' . date('Y-m-d H:i', $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('m-d H:i') . '数据获取start_time：' . date('Y-m-d H:i', $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", $msg);
            sleep(10);
        }
        if ($get_num > 20) {
            $msg = "分类ID:{$media_id}，获取数据失败次数超过20次，请检查请求参数";
            $this->_addLogs("update_temai_article_{$type}_error.log", $msg);
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求，请求超过20次，IP被封");
            exit;
        }
        $news_ids_arr = array_chunk($news_ids, 500);
        foreach ($news_ids_arr as $in_id) {
            $temp_ids     = M('temai_article')->where(array('news_id' => array('in', $in_id)))->getField('news_id', true);
            $new_ids_data = array_merge($new_ids_data, $temp_ids ? : array());
        }
        foreach ($new_ids_data as $id) {
            $update_data = array('go_detail_count' => $data[$id]['go_detail_count'], 'comments_count' => $data[$id]['comments_count']);
            M('temai_article')->where(array('news_id' => $id))->save($update_data);
            if (isset($data[$id]) && $data[$id]) {
                unset($data[$id]);
            }
        }
        if (count($data) == 0) {
            $msg = "分类ID:{$media_id}，已全部更新完成";
            $this->_addLogs('update_temai_article_success.log', $msg);
            $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求,已全部添加，仅更新");
            exit;
        }
        $add_data = array_chunk($data, 500);
        $model    = M('temai_article');
        $model->startTrans();
        try {
            foreach ($add_data as $db_data) {
                $model->addAll($db_data);
            }
            $model->commit();
            $msg = "分类ID:{$media_id}，采集遗漏数据";
            $this->_addLogs('update_temai_article_success.log', $msg);
        } catch (\Exception $e) {
            $model->rollback();
            $this->_addLogs('db_update_temai_article_error.log', $e->getMessage());
        }
        $this->_addLogs("update_temai_article_{$type}_{$media_id}.log", date('Y-m-d H:i:s') . "-结束更新请求，更新并添加数据完成");
    }

    /**
     * 更新文章数据
     *
     * @param $media_id
     * @param $start_time
     * @param $end_time
     * @param $type
     */
    public function _addArticle($media_id, $start_time, $end_time, $type) {
        if (!in_array($media_id, $this->cateArr)) {
            $msg = "文章类型不合法";
            $this->_addLogs("add_article_{$type}_{$media_id}.log", $msg);
            exit;
        }
        if (!$start_time || !$end_time) {
            $msg = "时间不能为空";
            $this->_addLogs("add_article_{$type}_{$media_id}.log", $msg);
            exit;
        }
        $get_num        = 0;
        $start_time     = strtotime($start_time);
        $end_time       = strtotime($end_time);
        $temp_time      = $start_time;
        $model          = M('temai_article_read');
        $one_proxy_data = $two_proxy_data = array();
        while (count($one_proxy_data) < 10) {
            $proxy_url      = 'http://kps.kdlapi.com/api/getkps/?orderid=944016966057031&num=10&pt=1&sep=1';
            $get_data       = $this->_get($proxy_url);
            $one_proxy_data = explode("\r\n", $get_data);
        }
        while (count($two_proxy_data) < 10) {
            $proxy_url      = 'http://kps.kdlapi.com/api/getkps/?orderid=974018832145997&num=10&pt=1&sep=1';
            $get_data       = $this->_get($proxy_url);
            $two_proxy_data = explode("\r\n", $get_data);
        }
        $proxy_data = array_merge($one_proxy_data, $two_proxy_data);
        while ($end_time < $temp_time) {
            if (date('i') == 58 || date('i') == 59) {
                $this->_addLogs("add_article_{$type}_{$media_id}.log", '请求数据已达极限，自动停止抓取数据！');
                break;
            }
            $param_arr = $this->_getParam();
            $param     = array(
                'media_id'       => $media_id,
                'page_type'      => 1,
                'max_behot_time' => $start_time,
                'count'          => 20,
                'version'        => 2,
                'platform'       => 'wap',
                'as'             => $param_arr['as'],
                'cp'             => $param_arr['cp'],
            );
            $url       = "https://www.toutiao.com/pgc/ma/";
            $num       = rand(0, 19);
            $proxy     = $proxy_data[$num];
            $res       = json_decode($this->_get($url, $param, $proxy), true);

            if ($res['message'] == 'success' && count($res['data']) > 0) {
                $get_num = 0;
                $data    = array();
                foreach ($res['data'] as $val) {
                    $article_id = $val['str_group_id'];
                    $count      = $model->where(array('article_id' => $article_id))->order('create_time desc')->getField('go_detail_count');
                    $count      = $count > 0 ? $count : 0;
                    $num        = $val['total_read_count'] - $count;
                    if ($article_id) {
                        $data[] = array(
                            'article_id'      => $val['str_group_id'],
                            'title'           => $val['title'],
                            'comments_count'  => $val['comment_count'],
                            'go_detail_count' => $val['total_read_count'],
                            'behot_time'      => strtotime($val['datetime']),
                            'article_genre'   => get_word($val['article_url'], "content_type=", '&') == 1 ? 2 : 1,
                            'media_id'        => $media_id,
                            'url'             => $val['article_url'],
                            'create_time'     => strtotime(date('Y-m-d H') . ":00:00"),
                            'create_user_id'  => get_word($val['article_url'], "create_user_id=", '&'),
                            'news_id'         => get_word($val['article_url'], "id=", '&'),
                            'num'             => $num,
                        );
                    }
                }
                if ($data) {
                    $model->addAll($data);
                }
                $temp_time  = $res['next']['max_behot_time'];
                $start_time = $temp_time;
                $msg        = date('m - d H:i') . '数据获取start_time：' . date('Y - m - d H:i', $start_time) . "，请求IP：{$proxy}，请求数据成功，分类ID:{$media_id}";
            } else {
                if ($get_num > 20) {
                    break;
                }
                $get_num++;
                $msg = date('m - d H:i') . '数据获取start_time：' . date('Y - m - d H:i', $start_time) . "，请求IP：{$proxy}，请求数据第{$get_num}次失败，分类ID:{$media_id}";
            }
            $this->_addLogs("add_article_{$type}_{$media_id}.log", $msg);
            sleep(5);
        }
    }


    /**
     * @param $url
     * @return mixed
     */
    protected function _getUrlInfo($url) {
        preg_match("/[0-9]+/", $url, $matches);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 下面两行为不验证证书和 HOST，建议在此前判断 URL 是否是 HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // $ret 返回跳转信息
        curl_exec($ch);
        // $info 以 array 形式返回跳转信息
        $info = curl_getinfo($ch);
        // 跳转后的 URL 信息
        $retURL = $info['url'];
        // 记得关闭curl
        curl_close($ch);
        $url_data = array(
            'news_id'        => get_word($retURL, "\?id=", ' & '),
            'create_user_id' => get_word($retURL, 'create_user_id = ', ' & '),
            'article_id'     => $matches[0],
            'article_genre'  => get_word($retURL, 'content_type = ', ' & ') == 1 ? 2 : 1,
            'url'            => $retURL,
        );
        return $url_data;
    }

    /**
     * @return array
     */
    protected function _getParam() {
        $i = time() - mt_rand(100, 300);
        $b = dechex($i);
        $t = strtoupper($b);
        $e = strtoupper(md5($i));
        $s = substr($e, 0, 5);
        $o = substr($e, -5);
        $a = $c = '';

        for ($n = 0; $n < 5; $n++) {
            $a .= substr($s, $n, 1) . substr($t, $n, 1);
        }
        for ($r = 0; $r < 5; $r++) {
            $c .= substr($t, $r + 3, 1) . substr($o, $r, 1);
        }
        return array('as' => "A1{$a}" . substr($t, -3), 'cp' => substr($t, 0, 3) . $c . 'E1');
    }

    /**
     * @param $url
     * @param array $params
     * @param null $proxy
     * @param string $cookie
     * @return bool|mixed
     */
    protected function _get($url, $params = array(), $proxy = null, $cookie = '') {
        $oCurl = curl_init();
        if ($proxy) {
            list($ip, $port) = explode(':', $proxy);
            curl_setopt($oCurl, CURLOPT_PROXY, $ip);
            curl_setopt($oCurl, CURLOPT_PROXYPORT, $port);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        }
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application / json, text / javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: https://www.toutiao.com/m6768458493/',
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 获取代理ip
     *
     * @param string $type
     * @return null
     */
    protected function _getProxyOld($type = 'add') {
        if ($type == 'add') {
            $proxy_data = array(
                "121.199.6.124:16817",
                "121.42.63.89:16817",

            );
            $num        = date('i') % 4;
        } else if ($type == 'update') {
            $proxy_data = array(
                "120.24.216.121:16817",
                "110.76.185.162:16817",
                "114.215.140.117:16817",
                "114.67.143.11:16817",
            );
            $num        = date('i') % 4;
        } else {
            $proxy_data = array(
                "122.114.82.64:16817",
                "47.92.127.154:16817",
                "116.62.113.134:16817",
                "122.114.214.159:16817"
            );
            $num        = date('i') % 4;
        }
        $ip = null;
        if (isset($proxy_data[$num])) {
            $ip = $proxy_data[$num];
        }
        return $ip;
    }

    /**
     * 获取代理ip
     * 122.114.82.64:16817
     * 110.76.185.162:16817
     *
     * @param $cate_id
     * @return mixed|null
     */
    protected function _getProxy($cate_id = null) {
        $proxy_data = array(
            '6768458493' => "121.199.6.124:16817",
            '5500903267' => "121.42.63.89:16817",
            '5501832587' => "120.24.216.121:16817",
            '5500838410' => "114.67.143.11:16817",//生活美食会
            '5500803015' => "114.215.140.117:16817",
            '5500367585' => "114.67.143.11:16817",//美妆丽人
            '5501679303' => "114.67.143.11:16817",//宝妈推荐
            '5501814762' => "114.67.143.11:16817",//酷奇潮玩
            '5500311915' => "116.62.113.134:16817",
            '5500358214' => "122.114.214.159:16817",
            '5500950648' => "47.92.127.154:16817"
        );
        $ip         = "114.67.143.11:16817";
        if (isset($proxy_data[$cate_id])) {
            $ip = $proxy_data[$cate_id];
        }
        return $ip;
    }

    /**
     * 头条高阅读量文章抓取
     */
    public function getHighReadNews() {
        $is_get_data = true;
        $repeat_num  = 0;
        $fail_num    = 0;
        $page        = I('get.page', 1, 'int');
        while ($is_get_data) {
            $str_time = date('Y-m-d', strtotime('-3 days')) . "00:00:00";
            $end_time = date('Y-m-d', strtotime('-1 days')) . "23:59:59";
            $news_url = "http://rym.quwenge.com/temai_article.php?uid=%s&str_time=%s&end_time=%s&count=%s&orderY=%s&page=%s&1=1";
            $url      = sprintf($news_url, '', $str_time, $end_time, 1, 1, $page);
            $res      = $this->_get($url);
            $res      = json_decode($res, true);
            if (count($res) == 0) {
                $fail_num++;
                if ($fail_num == 5) {
                    $is_get_data = false;
                }
                $msg = "第{$page}页文章抓取失败";
                $this->_addLogs('high_news.log', $msg);
                sleep(3);
                continue;
            }
            $data = array();
            foreach ($res as $article) {
                $create_user_id               = get_word($article['url'], 'create_user_id=', '&');
                $data[$article['article_id']] = array(
                    'news_id'         => $article['id'],
                    'article_id'      => $article['article_id'],
                    'title'           => $article['title'],
                    'tag'             => $article['tag'],
                    'comments_count'  => $article['comments_count'],
                    'go_detail_count' => $article['go_detail_count'],
                    'behot_time'      => $article['behot_time'],
                    'url'             => $article['url'],
                    'article_genre'   => $article['article_genre'],
                    'type'            => $article['type'],
                    'check_str'       => $article['check_str'],
                    'create_user_id'  => $create_user_id,
                );
            }
            $key_news_id = array_keys($data);
            $have_data   = M('high_read_article')->field('id,article_id')->where(array('article_id' => array('in', $key_news_id)))->index('article_id')->select();
            foreach ($have_data as $key => $val) {
                M('high_read_article')->where(array('id' => $val['id']))->save(array('comments_count' => $data[$key]['comments_count'], 'go_detail_count' => $data[$key]['go_detail_count']));
                unset($data[$key]);
            }
            if (count($data) == 0) {
                $repeat_num++;
                if ($repeat_num == 3) {
                    $is_get_data = false;
                }
                $msg = "第{$page}页文章抓取成功，但所有数据均已全部添加至文章库";
                $this->_addLogs('high_news.log', $msg);
                sleep(3);
                $page = $page + 1;
                continue;
            }
            $add_data = array_values($data);
            $add_res  = M('high_read_article')->addAll($add_data);
            if ($add_res) {
                $msg = "第{$page}页文章抓取成功并添加成功";
                $this->_addLogs('high_news.log', $msg);
                sleep(3);
                $page = $page + 1;
                continue;
            } else {
                $msg = "第{$page}页文章抓取成功,但添加失败";
                $this->_addLogs('high_news.log', $msg);
                sleep(3);
                continue;
            }
        }
    }

    /**
     * 抓取首图数据
     */
    public function getHighReadImg() {
        $end_time   = strtotime(date('Y-m-d')) - 1;
        $start_time = $end_time - 86399;
        $db_data    = M('temai_article')->field('id,article_id,title,url,create_user_id,behot_time')->where(array('go_detail_count' => array('gt', 10000), 'article_genre' => 1, 'behot_time' => array('between', array($start_time, $end_time))))->index('id')->order('behot_time desc,id desc')->select();
        if (count($db_data) == 0) {
            $msg = date('Y-m-d', strtotime('-1 days')) . "没有大于10000阅读量的文章";
            $this->_addLogs('high_img.log', $msg);
        }
        $data = $article_ids = $article_no_normal_ids = array();
        foreach ($db_data as $article) {
            $temp_num = 0;
            $content  = $this->_get($article['url']);
            $reg_exp  = '%<input type="hidden" id="mid" value="([0-9]{10})">%si';
            preg_match($reg_exp, $content, $match);
            $media_id = $match[1];
            if ($media_id) {
                $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
                preg_match($reg_exp, $content, $match);
                $match_data = json_decode($match[1], true);
                if (count($match_data) > 0) {
                    foreach ($match_data as $k => $item) {
                        if ($temp_num > 0) {
                            break;
                        }
                        if (isset($item['price']) && (strpos($item['real_url'], 'haohuo.snssdk.com') || strpos($item['real_url'], 'haohuo.jinritemai.com'))) {
                            $data[$item['id']] = array(
                                'title'         => $item['name'],
                                'img'           => $item['img'],
                                'shop_goods_id' => $item['shop_goods_id'],
                                'url'           => $item['user_url'],
                                'time'          => $article['behot_time'],
                                'media_id'      => $media_id,
                            );
                            $temp_num++;
                        }
                    }
                }
            }
        }
        if ($data) {
            $model = M();
            $model->startTrans();
            try {
                $add_data = array_chunk($data, 999);
                foreach ($add_data as $img) {
                    M('high_item_img')->addAll($img);
                }
                $model->commit();
                $msg = "添加至商品首图成功";
                $this->_addLogs('high_img.log', $msg);
            } catch (\Exception $e) {
                $model->rollback();
                $msg = $e->getMessage();
                $this->_addLogs('high_img.log', $msg);
            }
        } else {
            $msg = "组合商品首图数据失败！";
            $this->_addLogs('high_img.log', $msg);
        }

    }

    /**
     * 自动发文
     */
    public function autoSendArticle() {
        if (C('IS_AUTO_SEND_TOP') == 0) {
            echo "自动发文已关闭！";
            exit;
        }
        $media_id = I('get.media_id', '', 'trim');
        if (!in_array($media_id, $this->media_id)) {
            echo "文章类别信息错误，无法获取数据！";
            exit;
        }
        if ($media_id == '5500358214' || $media_id == '5500903267') {
            $account_id = 21;
        } else {
            $account_id = 20;
        }
        $cache_news_id = S('news_id_' . $media_id);
        $start_time    = strtotime(date('Y-m-d H') . ":00:00") - 14400;
        $end_time      = $start_time + 7199;
        $read_data     = M('temai_article_read')->field('max(go_detail_count) as count, news_id,title,article_id')->where(array('behot_time' => array('between', array($start_time, $end_time)), 'media_id' => $media_id, 'create_user_id' => array('not in', $this->create_user_id)))->group('news_id')->order('count desc')->limit(10)->select();
        $news_id       = $news_title = $news_article_id = array();
        foreach ($read_data as $val) {
            if ($val['news_id'] > 0 && !in_array($val['news_id'], $cache_news_id)) {
                $news_id[]                        = $val['news_id'];
                $news_title[$val['news_id']]      = $val['title'];
                $news_article_id[$val['news_id']] = $val['article_id'];
            }
        }
        if (count($news_id) > 0) {
            S('news_id_' . $media_id, $news_id);
            foreach ($news_id as $v) {
                $send_data = $this->_createSendData($v, $media_id, 'have_url');
                if (count($send_data['covers']) > 0 && $send_data['content']) {
                    $click_url = "http://tm.biuzhushou.cn/Index/index/article_id/{$news_article_id[$v]}";
                    $res       = $this->_sendTop($news_title[$v], $send_data['content'], json_encode($send_data['covers']), $media_id, $click_url);
                    $data      = array(
                        'account_id'      => $account_id,
                        'media_id'        => $media_id,
                        'title'           => $news_title[$v],
                        'json_covers_img' => json_encode($send_data['covers']),
                        'content'         => $send_data['content'],
                        'send_time'       => 0,
                        'add_time'        => time(),
                    );
                    $log_data  = array('time' => date('Y-m-d H:i:s'), 'news_id' => $v, 'media_id' => $media_id);
                    if ($res == false) {
                        $log_data['msg']     = '请求服务器失败，请稍后重试！';
                        $data['fail_reason'] = '请求服务器失败，请稍后重试！';
                    } else {
                        $res = json_decode($res, true);
                        if ($res['code'] == 0 && trim($res['message']) != 'error') {
                            $data['is_send'] = 1;
                        }
                        $data['fail_reason'] = $res['message'];
                        $log_data['msg']     = $res['message'];
                    }
                    $this->_addLogs('auto_send_article.log', $log_data);
                    M('top_line_album')->add($data);
                }

            }
        }

    }


    /**
     * 自动发文
     */
    public function autoSendWeiTop() {
        if (C('IS_AUTO_SEND_WEI_TOP') == 0) {
            echo "自动发文已关闭！";
            exit;
        }
        $media_id      = 5500358214;
        $cache_news_id = S('wei_top_' . $media_id);
        $start_time    = strtotime(date('Y-m-d H') . ":00:00") - 14400;
        $end_time      = $start_time + 7199;
        $read_data     = M('temai_article_read')->field('max(go_detail_count) as count, news_id,title')->where(array('behot_time' => array('between', array($start_time, $end_time)), 'media_id' => $media_id, 'create_user_id' => array('not in', $this->create_user_id)))->group('news_id')->order('count desc')->limit(10)->select();
        $news_id       = $news_title = array();
        foreach ($read_data as $val) {
            if ($val['news_id'] > 0 && !in_array($val['news_id'], $cache_news_id)) {
                $news_id[]                   = $val['news_id'];
                $news_title[$val['news_id']] = $val['title'];
            }
        }
        if (count($news_id) > 0) {
            S('wei_top_' . $media_id, $news_id);
            $cookie = M('top_line_account')->getFieldById(23, 'cookie');
            foreach ($news_id as $v) {
                $log_data             = array('time' => date('Y-m-d H:i:s'), 'title' => $news_title[$v]);
                $img_data             = $this->_createWeiTopImgData($v, $cookie);
                $log_data['img_data'] = $img_data;
                if (count($img_data) >= 3) {
                    $send_url        = "https://mp.toutiao.com/thread/add_thread/";
                    $send_data       = array(
                        'content'               => $news_title[$v],
                        'content_rich_span'     => array('links' => array()),
                        'forum_id'              => 6564242300,
                        'image_list'            => $img_data,
                        'mention_forum_id_list' => array(),
                        'mention_user_id_list'  => array(),
                    );
                    $res             = $this->_weiPost($send_url, json_encode($send_data), $cookie);
                    $res             = json_decode($res, true);
                    $log_data['res'] = $res;
                    if ($res['message'] == 'success') {
                        $this->_addLogs('auto_send_wei_top_success.log', $log_data);
                    } else {
                        $this->_addLogs('auto_send_wei_top_error.log', $log_data);
                    }
                } else {
                    $log_data['msg'] = '文章图片少于3张无法发文！';
                    $this->_addLogs('auto_send_wei_top_error.log', $log_data);
                }
            }
        } else {
            $this->_addLogs('auto_send_wei_top_success.log', date('Y-m-d H:i:s') . "所有文章均已发表！");
        }

    }

    /**
     * @param $news_id
     * @param $media_id
     * @return array
     */
    protected function _createSendData($news_id, $media_id, $send_type = 'not_url') {
        $url     = "https://temai.snssdk.com/article/feed/index?id={$news_id}";
        $content = $this->_get($url);
        $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
        preg_match($reg_exp, $content, $match);
        $match_data = json_decode($match[1], true);
        $content    = "";
        $covers     = array();
        $temp_num   = 0;
        if (count($match_data) > 0) {
            $content = '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095490fbc8e2690a" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095490fbc8e2690a" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
            foreach ($match_data as $k => $item) {
                $item_img      = $img_key = null;
                $shop_goods_id = str_replace('-', '', $item['shop_goods_id']);
                list($img, $_) = explode('?', $item['img']);
                if (strpos($img, 'ttcdn-tos')) {
                    $upload_res = $this->_uploadImg($img, $media_id);
                    if ($upload_res['status'] == 1) {
                        $item_img = $upload_res['url'];
                        $img_key  = $upload_res['img_key'];
                    } else {
                        $upload_res = $this->_uploadImg($img, $media_id);
                        if ($upload_res['status'] == 1) {
                            $item_img = $upload_res['url'];
                            $img_key  = $upload_res['img_key'];
                        }
                    }
                } else {
                    $item_img = $img;
                }
                if ($item_img) {
                    $temp_num++;
                    if (count($covers) < 3) {
                        $covers[] = array(
                            'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                            'url'          => $img,
                            'uri'          => $img_key,
                            'origin_uri'   => $img_key,
                            'ic_uri'       => $img_key,
                            'thumb_width'  => 700,
                            'thumb_height' => 700,
                        );
                    }
                    if ($send_type == 'not_url') {
                        $link_url = "https://haohuo.snssdk.com/views/product/item?id={$shop_goods_id}&fxg_req_id=&origin_type=4&origin_id=" . $this->source_id . "_3297144158254439459&new_source_type=9&new_source_id=120806&source_type=9&source_id=120806&come_from=0#tt_daymode=1&tt_font=m";
                        $content  .= '<div class="pgc-img"><img src="' . $item_img . '" data-ic="false" data-height="" data-width=""><p class="pgc-img-caption">如果喜欢本商品单击下面文字了解</p></div>';
                        $content  .= '<a class="pgc-link"  se_prerender_url="complete" href="' . $link_url . '" target="_blank"><p>' . $item['name'] . '</p></a>';
                        $content  .= '<p><br></p>';
                    } else {
                        $content .= '<div class="pgc-img"><img class="" src="' . $item_img . '" data-ic="false" data-ic-uri="" image_type="1"><p class="pgc-img-caption">如果喜欢本商品单击文末了解更多</p></div><p>' . $item['name'] . '</p>';
                    }
                }
            }
            $content .= '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095525f71ec7fed4" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095525f71ec7fed4" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
        }
        if ($temp_num >= 3) {
            return array('content' => $content, 'covers' => $covers);
        } else {
            return array('content' => '', 'covers' => array());
        }

    }

    /**
     * @param $news_id
     * @param $media_id
     * @return array
     */
    protected function _createWeiTopImgData($news_id, $cookie) {
        $url     = "https://temai.snssdk.com/article/feed/index?id={$news_id}";
        $content = $this->_get($url);
        $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
        preg_match($reg_exp, $content, $match);
        $match_data = json_decode($match[1], true);
        $img_data   = array();
        if (count($match_data) > 0) {
            foreach ($match_data as $k => $item) {
                list($img, $_) = explode('?', $item['img']);
                if (count($img_data) < 9) {
                    $img_res = $this->_uploadWeiTopImg($img, $cookie);
                    if ($img_res['status'] == 1) {
                        $img_data[] = $img_res['img_key'];
                    }
                }
            }
        }
        return $img_data;
    }


    /**
     * 获取头条默认账号信息
     */
    protected function _getTopLineCookie() {
        $data = S('top_line_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(1, 'cookie');
            if ($data) {
                S('top_line_cookie', $data);
            }
        }
        return $data;
    }

    /**
     * @param $media_id
     * @return mixed
     */
    protected function _getCookie($media_id) {
        $data = S('top_line_cookie_media');
        if (!$data) {
            $data['5500903267'] = M('top_line_account')->getFieldById(21, 'cookie');
            $data['5500358214'] = M('top_line_account')->getFieldById(21, 'cookie');
            $data['8888888888'] = M('top_line_account')->getFieldById(23, 'cookie');
            if ($data) {
                S('top_line_cookie_media', $data);
            }
        }
        return $data[$media_id];
    }

    /**
     * @param $file_url
     * @param $media_id
     * @return array
     */
    protected function _uploadImg($file_url, $media_id) {
        $url_path = dirname(APP_PATH) . '/www/Uploads/temp_.' . $media_id . '.jpg';
        $file_url = str_replace('https', 'http', $file_url);
        $content  = file_get_contents($file_url);
        file_put_contents($url_path, $content);
        $obj   = new \CurlFile($url_path);
        $param = array('upfile' => $obj);
        $data  = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
        unlink($url_path);
        if (strtolower($data['state']) == 'success') {
            $url = "http://p2.pstatp.com/large/{$data['original']}";
            return array('status' => 1, 'info' => 'ok', 'url' => $url, 'img_key' => $data['web_uri'], 'width' => $data['width'], 'height' => $data['height'], 'image_type' => $data['image_type']);
        } else {
            return array('status' => 0, 'info' => '上传失败！');
        }
    }

    /**
     * @param $file_url
     * @return array
     */
    protected function _uploadWeiTopImg($file_url, $cookie) {
        if (strpos($file_url, 'http') === false) {
            $file_url = "https:" . $file_url;
        }
        $url_path = dirname(APP_PATH) . '/www/Uploads/auto_wei_top_temp.jpg';
        $file_url = str_replace('https', 'http', $file_url);
        $content  = file_get_contents($file_url);
        file_put_contents($url_path, $content);
        $obj   = new \CurlFile($url_path);
        $param = array('photo' => $obj);
        $data  = json_decode($this->_file("https://mp.toutiao.com/upload_photo/?type=json", $param, $cookie), true);
        unlink($url_path);
        if (strtolower($data['message']) == 'success') {
            return array('status' => 1, 'info' => 'ok', 'url' => $data['web_url'], 'img_key' => $data['web_uri']);
        } else {
            return array('status' => 0, 'info' => '上传失败！');
        }
    }

    /**
     * @param $title
     * @param $content
     * @param $covers
     * @param $media_id
     * @param null $click_url
     * @return bool|mixed
     */
    protected function _sendTop($title, $content, $covers, $media_id, $click_url = null) {
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
            'extern_link'            => $click_url,
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
        if (empty($click_url)) {
            unset($post_data['extern_link']);
        }
        $res = $this->_post($this->send_url, $post_data, $this->_getCookie($media_id));
        return $res;
    }


    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _weiPost($url = '', $params = '', $cookie = '') {
        $ch = curl_init($url);
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'cookie: ' . $cookie,
            'X-Requested-With:XMLHttpRequest',
        ));
        $sContent = curl_exec($ch);
        $aStatus  = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _post($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'Referer: ' . $this->http_referer,
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 更新阅读量
     */
    public function updateRead() {
        M()->execute("truncate ytt_temai_article_temp");
        $time    = strtotime(date("Y-m-d"));
        $db_data = M()->query("select * from (select article_id,go_detail_count,title,comments_count from ytt_temai_article_read where behot_time > {$time} order by go_detail_count desc) as temp_article group by article_id");
        if (count($db_data) > 0) {
            $data = array_chunk($db_data, 999);
            foreach ($data as $val) {
                M('temai_article_temp')->addAll($val);
            }
            M()->execute("update ytt_temai_article,ytt_temai_article_temp set ytt_temai_article.go_detail_count=ytt_temai_article_temp.go_detail_count,ytt_temai_article.comments_count=ytt_temai_article_temp.comments_count where ytt_temai_article.article_id=ytt_temai_article_temp.article_id");
            $this->_addLogs('update_read.log', date('Y-m-d H:i:s') . ' 数据处理完成！');
        } else {
            $this->_addLogs('update_read.log', date('Y-m-d H:i:s') . ' 暂时没有数据需要处理！');
        }
    }

    /**
     * 添加文章
     */
    public function addArticleRead() {
        $end_time   = time();
        $start_time = time() - 7200;
        $db_data    = M('temai_article_read')->index('news_id')->where(array('behot_time' => array('between', array($start_time, $end_time)), 'news_id' => array('gt', 0)))->group('news_id')->select();
        if (count($db_data) > 0) {
            $have_article_id = M('temai_article')->where(array('news_id' => array('in', array_keys($db_data))))->getField('news_id', true);
            if (count($have_article_id) > 0) {
                foreach ($have_article_id as $val) {
                    if (isset($db_data[$val]) && $db_data[$val]) {
                        unset($db_data[$val]);
                    }
                }
            }
            $data  = array_chunk($db_data, 999);
            $model = M('temai_article');
            $model->startTrans();
            try {
                foreach ($data as $db_data_row) {
                    $model->addAll($db_data_row);
                }
                $model->commit();
                $this->_addLogs('add_article_read.log', date('Y-m-d H:i:s') . ' 数据处理成功');
            } catch (\Exception $e) {
                $model->rollback();
                $this->_addLogs('add_article_read.log', date('Y-m-d H:i:s') . ' 数据处失败！' . $e->getMessage());
            }
        } else {
            $this->_addLogs('add_article_read.log', date('Y-m-d H:i:s') . ' 暂时没有文章需要添加！');
        }
    }

    /**
     * 更新阅读量
     */
    public function updateArticleRead() {
        $start_time = date('Y-m-d', strtotime('-7 days'));
        $end_time   = date('Y-m-d', strtotime('-1 days'));
        $page       = 1;
        $is_have    = true;
        while ($is_have) {
            $url      = "http://rym.quwenge.com/temai_article.php?str_time={$start_time}&end_time={$end_time}&page={$page}";
            $get_data = $this->_get($url);
            $get_data = json_decode($get_data, true);
            if (count($get_data) == 0) {
                $this->_addLogs('update_article_read.log', date('Y-m-d H:i:s') . "第{$page}页文章未找到文章数据！");
                break;
            }
            if (count($get_data) < 100) {
                $is_have = false;
            }
            $article_data = array();
            foreach ($get_data as $val) {
                $article_data[$val['article_id']] = $val['go_detail_count'];
            }
            if (count($article_data) == 0) {
                $this->_addLogs('update_article_read.log', date('Y-m-d H:i:s') . "第{$page}页文章未匹配到文章ID！");
                $page++;
                continue;
            }
            $have_article_id = M('temai_article')->where(array('article_id' => array('in', array_keys($article_data))))->getField('article_id', true);
            foreach ($have_article_id as $val) {
                M('temai_article')->where(array('article_id' => $val))->save(array('go_detail_count' => $article_data[$val]));
            }
            $this->_addLogs('update_article_read.log', date('Y-m-d H:i:s') . "第{$page}页文章数据处理完成！");
            $page++;
        }
    }

    /**
     * 自动发商品
     */
    public function autoSendItem() {
        $goods = M('dtk_goods')->where(array('is_auto' => 0))->order('id desc')->find();
        //var_dump($goods);exit;
        if (empty($goods)) {
            $msg = "商品已发送完成！";
            $this->_addLogs('auto_send_item.log', date('Y-m-d H:i:s') . $msg);
            die($msg);
        }
        $img_res = $this->_uploadImg($goods['pic'], '8888888888');
        if ($img_res['status'] == 0) {
            $this->_addLogs('auto_send_item.log', date('Y-m-d H:i:s') . $img_res['info']);
            die($img_res['info']);
        }

        $link_res = $this->_getShortLink($goods['goods_id']);
        if ($link_res['status'] == 0) {
            $this->_addLogs('auto_send_item.log', date('Y-m-d H:i:s') . $link_res['info']);
            die($link_res['info']);
        }
        $img      = $img_res['url'];
        $img_key  = $img_res['img_key'];
        $covers[] = array(
            'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
            'url'          => $img,
            'uri'          => $img_key,
            'origin_uri'   => $img_key,
            'ic_uri'       => $img_key,
            'thumb_width'  => 800,
            'thumb_height' => 800,
        );
        $title    = $goods['short_title'];
        //$desc      = $goods['desc'];
        $content   = '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095490fbc8e2690a" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095490fbc8e2690a" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
        $content   .= '<div class="pgc-img"><img class="" src="' . $img . '" data-ic="false" data-ic-uri="" image_type="1"><p class="pgc-img-caption">领' . $goods['coupon_money'] . '优惠券，券后价：' . $goods['coupon_price'] . '</p></div><p>' . $goods['title'] . '</p>';
        $content   .= '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095525f71ec7fed4" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095525f71ec7fed4" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
        $click_url = $this->_getShortUrl($link_res['url']);
        $res       = $this->_sendTop($title, $content, json_encode($covers), '8888888888', $click_url);
        if ($res == false) {
            $msg = '请求服务器失败，请稍后重试！';
        } else {
            $res = json_decode($res, true);
            if ($res['code'] == 0) {
                M('dtk_goods')->save(array('id' => $goods['id'], 'is_auto' => 1));
            }
            $msg = $res['message'];
        }
        $this->_addLogs('auto_send_item.log', $msg);
        die($msg);

    }

    /**
     * @param $goods_id
     * @param null $pid
     * @param null $token
     * @return mixed
     */
    protected function _getShortLink($goods_id, $pid = null, $token = null) {
        // 迷离团队请求配置参数
        if (!$pid) {
            $pid = 'mm_121610813_42450934_228388649';
        }
        if (!$token) {
            $token = C('TB_ACCESS_TOKEN.default_token');
        }
        $pid_info  = explode('_', $pid);
        $url       = 'http://tbapi.00o.cn/highapi.php';
        $param     = 'item_id=%s&adzone_id=%s&platform=1&site_id=%s&token=%s';
        $param     = sprintf($param, $goods_id, $pid_info[3], $pid_info[2], $token);
        $Http      = new Http();
        $mi_li_res = $Http->post($url, $param);
        $res       = json_decode($mi_li_res, true);
        if ($res && $res['result']['data']['coupon_click_url']) {
            $click_url = $res['result']['data']['coupon_click_url'];
            return array('status' => 1, 'url' => $click_url, 'info' => 'ok');
        } else {
            return array('status' => 0, 'info' => $res['sub_msg']);
        }
    }

    /**
     * 把长链接转换为短链接
     *
     * @param $long_url
     * @return string
     */
    public function _getShortUrl($long_url) {
        $httpObj          = new Http();
        $httpObj->timeOut = 3;
        $time             = time();
        if (true) {//$time % 2 == 0
            //  新浪url
            $sina_url   = 'http://api.t.sina.com.cn/short_url/shorten.json';
            $sina_param = array('source' => '3271760578', 'url_long' => $long_url);
            $tmp        = json_decode($httpObj->get($sina_url, $sina_param), true);
            $short_url  = isset($tmp[0]['url_short']) ? $tmp[0]['url_short'] : urldecode($long_url);
        } else {
            // 生成短链接url 缩我  get
            $suo_url   = 'http://suo.im/api.php';
            $suo_param = array('url' => urldecode($long_url));
            $tmp       = $httpObj->get($suo_url, $suo_param);
            $short_url = $tmp ? : urldecode($long_url);
        }
        return $short_url;
    }

    /**
     * 抓取藏金阁文章
     */
    public function taoJinGeArticle() {
        $page     = 1;
        $http     = new Http();
        $add_data = array();
        $model    = M('tao_jin_ge_article');
        $is_num   = 0;
        while (true) {
            $data = array();
            $url  = "http://taojinge.wangzherongyao.cn/platform/article/queryByPage.do?userId=459b38f308d7480a8048cc3d143382a0&currentPage={$page}&articleStatus=0";
            $res  = $http->get($url);
            $res  = json_decode($res, true);
            if ($res['result'] == 'success') {
                foreach ($res['data'] as $val) {
                    $is_num = $model->where(array('article_id' => $val['articleId']))->count('id');
                    if ($is_num > 0) {
                        break;
                    }
                    $reg_exp = '%<img src="(.*?)" .*?>%si';
                    preg_match_all($reg_exp, $val['articleContent'], $match);
                    $img_html = $match[0];
                    $img_data = $match[1];
                    if (count($img_data) < 3) {
                        continue;
                    }
                    $data[] = array(
                        'article_url' => $val['copyLink'],
                        'article_id'  => $val['articleId'],
                        'title'       => $val['articleTitle'],
                        'content'     => $val['articleContent'],
                        'cate_name'   => $val['articleClassify'],
                        'img_data'    => json_encode($img_data),
                        'img_html'    => json_encode($img_html),
                        'add_time'    => $val['articleCreateTime'],
                    );
                }
                if (count($data) > 0) {
                    $add_data = array_merge($add_data, $data);
                }
                if (count($res['data']) < 10) {
                    break;
                }
                if ($is_num > 0) {
                    break;
                }
            } else {
                break;
            }
            $page++;
        }
        if ($add_data) {
            $model->addAll($add_data);
            $add_count = count($add_data);
            echo "添加成功，共添加{$add_count}条记录！";
        } else {
            echo "没有新文章需要添加！";
        }
    }

    /**
     * 修改成头条需要的格式
     */
    public function updateTaoJinGeArticle() {
        $info         = M('tao_jin_ge_article')->where(array('is_normal' => 0,'fail_num'=>array('lt',3)))->order('add_time desc')->find();
        $content      = $info['content'];
        $img_data     = json_decode($info['img_data'], true);
        $img_html     = json_decode($info['img_html'], true);
        $new_img_data = $covers = array();
        $error_info   = $new_content = null;
        foreach ($img_data as $val) {
            $res = $this->_moveImg($val);
            if ($res['status'] == -1) {
                $up_res = $this->_uploadImg($val, '99999999');
                if ($up_res['status'] == 1) {
                    $up_img_data    = array(
                        'image'      => $up_res['url'],
                        'img_key'    => $up_res['img_key'],
                        'image_type' => $up_res['img_key'],
                        'width'      => $up_res['width'],
                        'height'     => $up_res['height']
                    );
                    $new_img_data[] = array('img_data' => $up_img_data, 'src_img' => $val);
                } else {
                    $error_info = $up_res['info'];
                    break;
                }
            }
            $new_img_data[] = array('img_data' => $res['data'], 'src_img' => $val);
        }
        if (!empty($error_info)) {
            $this->_addLogs('update_tao_jin_ge_article', $error_info);
            M('tao_jin_ge_article')->where(array('id'=>$info['id']))->setInc('fail_num');
            exit($error_info);
        }
        foreach ($img_html as $k => $v) {
            $img_html = '<img class="" src="' . $new_img_data[$k]['img_data']['image'] . '" data-ic="false" data-ic-uri="" data-height="0" data-width="0" image_type="' . $new_img_data[$k]['img_data']['image_type'] . '" web_uri="' . $new_img_data[$k]['img_data']['img_key'] . '" img_width="' . $new_img_data[$k]['img_data']['width'] . '" img_height="' . $new_img_data[$k]['img_data']['height'] . '">';
            $content  = str_replace($v, $img_html, $content);
            if (count($covers) < 3) {
                $covers[] = array(
                    'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                    'url'          => $new_img_data[$k]['img_data']['image'],
                    'uri'          => $new_img_data[$k]['img_data']['img_key'],
                    'origin_uri'   => $new_img_data[$k]['img_data']['img_key'],
                    'ic_uri'       => $new_img_data[$k]['img_data']['img_key'],
                    'thumb_width'  => $new_img_data[$k]['img_data']['width'],
                    'thumb_height' => $new_img_data[$k]['img_data']['height'],
                );
            }
        }
        M('tao_jin_ge_article')->where(array('id' => $info['id']))->save(array('send_content' => $content, 'covers' => json_encode($covers), 'is_normal' => 1));
        exit('处理成功！');
    }

    /**
     * @param $file_url
     * @return array
     */
    protected function _moveImg($file_url) {
        $url   = "https://mp.toutiao.com/tools/catch_picture/";
        $param = array('upfile' => $file_url, 'version' => 2);
        $res   = $this->_post($url, $param, $this->_getTopLineCookie());
        $res   = json_decode($res, true);
        if ($res['message'] == 'error') {
            return array('status' => -1, 'info' => 'cookie失效，无法上传图片');
        }
        if (count($res['images']) == 0) {
            return array('status' => -1, 'info' => '图片上传失败！');
        }
        $data = array(
            'image'      => $res['url'],
            'img_key'    => $res['images'][0]['original'],
            'image_type' => $res['images'][0]['image_type'],
            'width'      => $res['images'][0]['width'],
            'height'     => $res['images'][0]['height']
        );
        return array('status' => 1, 'data' => $data);
    }


}