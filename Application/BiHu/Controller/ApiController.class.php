<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/21
 * Time: 9:12
 */

namespace BiHu\Controller;

/**
 * Class ApiController
 *
 * @package BiHu\Controller
 */
class ApiController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 文章抓取
     */
    public function addArticle() {
        $json = I('post.json', '', 'trim');
        $json = json_decode($json, true);
        if (count($json) > 0) {
            $data = $add_data = array();
            foreach ($json as $val) {
                $data[$val['id']] = array(
                    'article_id'      => $val['id'],
                    'article_title'   => $val['title'],
                    'article_content' => $val['snapcontent'],
                    'article_img'     => 'https://bihu2001.oss-cn-shanghai.aliyuncs.com/' . $val['snapimage'],
                    'comment_num'     => $val['cmts'],
                    'up_num'          => $val['ups'],
                    'money'           => $val['money'],
                    'author_id'       => $val['userId'],
                    'author_name'     => $val['userName'],
                    'author_icon'     => 'https://bihu2001.oss-cn-shanghai.aliyuncs.com/' . $val['userIcon'],
                    'create_time'     => $val['createTime'] / 1000
                );
            }
            $article_id     = array_keys($data);
            $db_article_id  = M('article')->where(array('article_id' => array('in', $article_id)))->getField('article_id', true);
            $db_article_id  = $db_article_id ? $db_article_id : array();
            $add_article_id = array_diff($article_id, $db_article_id);
            foreach ($db_article_id as $v) {
                M('article')->where(array('article_id' => $v))->save(array('money' => $data[$v]['money'], 'comment_num' => $data[$v]['comment_num'], 'up_num' => $data[$v]['up_num']));
            }
            if ($add_article_id) {
                foreach ($add_article_id as $v) {
                    $add_data[] = $data[$v];
                }
                M('article')->addAll($add_data);
                die(json_encode(array('status' => 1, 'article_id' => array_values($add_article_id))));
            } else {
                die(json_encode(array('status' => 0, 'info' => '没有新数据！')));
            }
        } else {
            die(json_encode(array('status' => 0, 'info' => '请求数据不合法！')));
        }
    }

    /**
     * 记录点赞日志
     */
    public function addUpLog() {
        $article_id = I('post.article_id', 0, 'int');
        $account_id = I('post.account_id', 0, 'int');
        if (empty($article_id)) {
            die(json_encode(array('status' => 0, 'info' => '文章ID不能为空！')));
        }
        if (empty($account_id)) {
            die(json_encode(array('status' => 0, 'info' => '点赞用户ID不能为空！')));
        }
        $title        = M('article')->where(array('article_id' => $article_id))->getField('article_title');
        $account_name = M('account')->where(array('user_id' => $account_id))->getField('username');
        $data         = array('article_id' => $article_id, 'title' => $title, 'account_id' => $account_id, 'account_name' => $account_name, 'create_time' => time());
        $res          = M('article_up_log')->add($data);
        if ($res) {
            die(json_encode(array('status' => 1, 'info' => '点赞成功')));
        } else {
            die(json_encode(array('status' => 0, 'info' => '点赞失败！')));
        }
    }

    /**
     * 测试代理IP访问
     */
    public function proxy() {
        die(json_encode($_SERVER));
    }

}