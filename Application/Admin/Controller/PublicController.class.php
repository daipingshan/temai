<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/13
 * Time: 14:51
 */

namespace Admin\Controller;


class PublicController extends CommonController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 跳转头条地址
     */
    public function index() {
        $url = "https://temai.snssdk.com/article/feed/index?id=9809586&subscribe=5501832587&source_type=27&content_type=2&create_user_id=7665&classify=16&adid=__AID__";
        header("Location:$url");
    }

    /**
     * 文章预览
     */
    public function article() {
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