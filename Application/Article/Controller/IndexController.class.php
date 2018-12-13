<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/11/2
 * Time: 14:51
 */

namespace Article\Controller;

use Common\Controller\CommonBaseController;

class IndexController extends CommonBaseController {

    /**
     * 特卖文章详情
     */
    public function index() {
        $article_id = I('get.article_id', '', 'trim');
        if (empty($article_id)) {
            die("文章不存在，无法查看！");
        }
        $url  = "https://temai.snssdk.com/article/feed/index?id={$article_id}";
        $html = $this->_get($url);
        $html = str_replace($article_id, '6618817498085065224', $html);
        echo $html;
    }
}