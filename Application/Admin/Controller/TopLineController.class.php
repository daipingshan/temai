<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/5
 * Time: 8:50
 */

namespace Admin\Controller;

use Common\Org\Http;
use Common\Org\OpenSearch;
use Common\Org\TranslateSDK;

/**
 * Class TopLineController
 *
 * @package Admin\Controller
 */
class TopLineController extends CommonController {
    /**
     * 发文地址
     *
     * @var string
     */
    private $send_url = "https://mp.toutiao.com/core/article/edit_article_post/?source=mp&type=figure";

    /**
     * 获取商品详情地址
     *
     * @var string
     */
    private $item_info_url = "https://mp.toutiao.com/article/get_product_info/";

    /**
     * 上传图片地址
     *
     * @var string
     */
    private $upload_url = "https://mp.toutiao.com/tools/upload_picture/?type=ueditor&pgc_watermark=1&action=uploadimage&encode=utf-8";
    /**
     * 查询商品地址
     *
     * @var string
     */
    private $search_url = "http://www.51taojinge.com/api/temai_select.php?uid=%s&search=%s&count=%s&pingtai=%s&str_time=%s&end_time=%s&orderY=%s&page=%s&1=1";

    /**
     * 头条文章查询
     *
     * @var string
     */
    private $search_news_url = "http://rym.quwenge.com/temai_article.php??uid=%s&count=%s&str_time=%s&end_time=%s&orderY=%s&page=%s&1=1";

    /**
     * @var string
     */
    protected $sale_item_info_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList?keyword_type=platform_sku_id&keyword_value=%s&keyword_type_shop=word&platform=3";

    /**
     * @var array
     */
    private $item_cate = array(
        array('uid' => '', 'name' => '全部'),
        array('uid' => '5569547953', 'name' => '每日穿衣之道'),
        array('uid' => '5568158065', 'name' => '辣妈潮宝'),
        array('uid' => '5572814229', 'name' => '美妆课堂'),
        array('uid' => '5573124268', 'name' => '潮男周刊'),
        array('uid' => '5573658957', 'name' => '会生活'),
        array('uid' => '5570589814', 'name' => '美食大搜罗'),
        array('uid' => '5571864339', 'name' => '户外行者'),
        array('uid' => '5573716916', 'name' => '文娱多宝阁'),
        array('uid' => '5571749564', 'name' => '数码极客'),
        array('uid' => '5565295982', 'name' => '爱车族'),
    );

    /**
     * 头条账号管理
     */
    public function index() {
        $count = M('top_line_account')->count();
        $page  = $this->pages($count, $this->limit);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('top_line_account')->limit($limit)->order('id desc')->select();
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 添加账号
     */
    public function addAccount() {
        $this->display('account');
    }

    /**
     * 修改账号
     */
    public function updateAccount() {
        $id   = I('get.id', 0, 'int');
        $info = M('top_line_account')->find($id);
        $this->assign('info', $info);
        $this->display('account');
    }

    /**
     * 保存账号
     */
    public function saveAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id       = I('post.id', 0, 'int');
        $username = I('post.username', '', 'trim');
        $cookie   = I('post.cookie', '', 'trim');
        $media_id = I('post.media_id', '', 'trim');
        if (!$username) {
            $this->error('账号名称不能为空！');
        }
        if (!$cookie) {
            $this->error('cookie信息不能为空！');
        }
        if (!$media_id) {
            $this->error('头条ID不能为空！');
        }
        $data = array('username' => $username, 'cookie' => $cookie, 'update_time' => time(), 'media_id' => $media_id);
        if ($id > 0) {
            $count = M('top_line_account')->where(array('username' => $username, 'id' => array('neq', $id)))->count('id');
            if ($count > 0) {
                $this->error('账号名称已存在！');
            }
            $data['id'] = $id;
            $res        = M('top_line_account')->save($data);
            if ($res !== false) {
                S('top_line_account', null);
                if ($id == 1) {
                    S('top_line_cookie', null);
                }
                if ($id == 8) {
                    S('wei_top_cookie', null);
                }
                S('top_line_cookie_media', null);
                $this->success('修改成功');
            } else {
                $this->error('修改失败！');
            }
        } else {
            $count = M('top_line_account')->where(array('username' => $username))->count('id');
            if ($count > 0) {
                $this->error('账号名称已存在！');
            }
            $data['add_time'] = time();
            $data['admin_id'] = $this->user_id;
            $res              = M('top_line_account')->add($data);
            if ($res) {
                S('top_line_account', null);
                S('top_line_cookie_media', null);
                $this->success('添加成功');
            } else {
                $this->error('添加失败！');
            }
        }

    }

    /**
     * 审核账号
     */
    public function setAccountStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('top_line_account')->where(array('id' => $id))->setField('status', 1);
        if ($res) {
            $this->success('审核成功');
        } else {
            $this->error('审核失败！');
        }
    }

    /**
     * 删除账号
     */
    public function deleteAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('top_line_account')->delete($id);
        if ($res) {
            S('top_line_account', null);
            S('top_line_cookie_media', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 查看该账号的基本信息
     */
    public function openInfo() {
        $id   = I('get.id', 0, 'int');
        $info = M('top_line_account')->find($id);
        if (empty($info)) {
            $this->error('账号信息不存在！');
        }
        $url = "https://mp.toutiao.com/core/article/new_article/?article_type=3&format=json";
        $res = $this->_get($url, array(), $info['cookie']);
        if ($res === false) {
            $this->error('服务器请求失败！');
        }
        $res = json_decode($res, true);
        if (isset($res['user_name'])) {
            if (empty($info['media_id'])) {
                M('top_line_account')->where(array('id' => $id))->setField('media_id', $res['media']['creator_id']);
            }
            $this->success($res['user_name']);
        } else {
            $this->error('cookie信息已过期！');
        }
    }

    /**
     * 获取头条商品
     */
    public function itemsList() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $media_id      = I('get.media_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $time          = I('get.time', '', 'trim,urldecode');
        $where         = array('status' => 1);
        $query         = "status:'1'";
        $filter        = null;
        if ($shop_goods_id) {
            $query                  .= "AND shop_goods_id:'{$shop_goods_id}'";
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($media_id) {
            $query             .= "AND media_id:'{$media_id}'";
            $where['media_id'] = $media_id;
        }
        if ($keyword) {
            $query                                                             .= " AND keyword:'{$keyword}'";
            $where['top_line_article_title|name|description|description_vice'] = array('like', "%{$keyword}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time                           = strtotime($start_time);
                $end_time                             = strtotime($end_time);
                $filter                               = "top_line_article_behot_time>{$start_time} AND top_line_article_behot_time<{$end_time}";
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        $cache_data = $this->_getItemCache();
        if ($this->openSearchStatus === true) {
            $obj       = new OpenSearch();
            $count     = $obj->searchCount($query, $filter);
            $p         = I('get.p', 1, 'int');
            $page      = $this->pages($count, $this->limit);
            $start_num = ($p - 1) * $this->limit;
            $open_data = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->limit);
            $db_data   = $open_data['data'];
        } else {
            $count   = M('temai_items')->where($where)->count();
            $page    = $this->pages($count, $this->limit);
            $limit   = $page->firstRow . ',' . $page->listRows;
            $db_data = M('temai_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
        }
        $data = array();
        foreach ($db_data as $key => $item) {
            $data[$key * 2 + 1]['id']            = $item['shop_goods_id'];
            $data[$key * 2 + 1]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 1]['type']          = '放心购';
            $data[$key * 2 + 1]['img']           = $item['img'];
            $data[$key * 2 + 1]['price']         = $item['price'];
            $data[$key * 2 + 1]['url']           = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 1]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 1]['title']         = $item['name'];
            $data[$key * 2 + 1]['describe_info'] = $item['description'] . $item['description_vice'];
            $data[$key * 2 + 1]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 1]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 1]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 1]['tmall_url']     = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 1]['post_data']     = json_encode($data[$key * 2 + 1]);
            $data[$key * 2 + 2]['id']            = $item['shop_goods_id'];
            $data[$key * 2 + 2]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 2]['type']          = '放心购';
            $data[$key * 2 + 2]['img']           = $item['img_vice'];
            $data[$key * 2 + 2]['price']         = $item['price'];
            $data[$key * 2 + 2]['url']           = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 2]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 2]['title']         = $item['name'];
            $data[$key * 2 + 2]['describe_info'] = $item['description'] . $item['description_vice'];
            $data[$key * 2 + 2]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 2]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 2]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 2]['tmall_url']     = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 2]['post_data']     = json_encode($data[$key * 2 + 2]);
            if (isset($cache_data[$item['shop_goods_id']])) {
                $data[$key * 2 + 1]['is_add'] = 1;
                $data[$key * 2 + 2]['is_add'] = 1;
            } else {
                $data[$key * 2 + 1]['is_add'] = 0;
                $data[$key * 2 + 2]['is_add'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'cart_count' => count($cache_data), 'time' => $time));
        $this->display();
    }


    /**
     * 获取头条商品
     */
    public function itemsUrlList() {
        $url        = I('get.url', '', 'trim');
        $cache_data = $this->_getItemCache();
        if ($url) {
            $content = $this->_get($url);
            $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
            preg_match($reg_exp, $content, $match);
            $match_data = json_decode($match[1], true);
            $data       = array();
            foreach ($match_data as $k => $item) {
                if (isset($item['price']) && (strpos($item['real_url'], 'haohuo.snssdk.com') || strpos($item['real_url'], 'haohuo.jinritemai.com'))) {
                    list($img, $_) = explode('?', $item['img']);
                    $data[$item['id']] = array(
                        'shop_type'          => $item['shop_type'],
                        'shop_goods_id'      => $item['shop_goods_id'],
                        'price_tag_position' => $item['price_tag_position'],
                        'img'                => $img,
                        'goods_json'         => $item['goods_json'],
                        'name'               => $item['name'],
                        'price'              => $item['price'],
                        'description'        => $item['description'],
                        'real_url'           => 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'],
                        'self_charging_url'  => $item['self_charging_url'],
                    );
                }
                if (isset($item['commodity']) && isset($data[$item['commodity']['id']]) && !isset($data[$item['commodity']['id']]['img_vice'])) {
                    list($img_vice, $_) = explode('?', $item['location']);
                    $data[$item['commodity']['id']]['img_vice']         = $img_vice;
                    $data[$item['commodity']['id']]['description_vice'] = $item['description'];
                }
            }
            if (count($match_data) == 0 || empty($data)) {
                $this->assign('info', '链接地址不合法或文章不符合规则！');
            } else {
                $_data = array_values($data);
                $data  = array();
                foreach ($_data as $key => $item) {
                    $data[$key * 2 + 1]['id']            = $item['shop_goods_id'];
                    $data[$key * 2 + 1]['temai_id']      = $item['top_line_article_id'] . $item['shop_goods_id'];
                    $data[$key * 2 + 1]['type']          = '放心购';
                    $data[$key * 2 + 1]['img']           = $item['img'];
                    $data[$key * 2 + 1]['price']         = $item['price'];
                    $data[$key * 2 + 1]['url']           = $item['real_url'];
                    $data[$key * 2 + 1]['title']         = $item['name'];
                    $data[$key * 2 + 1]['describe_info'] = $item['description'] . $item['description_vice'];
                    $data[$key * 2 + 1]['taobao_id']     = $item['shop_goods_id'];
                    $data[$key * 2 + 1]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
                    $data[$key * 2 + 1]['tmall_url']     = $item['real_url'];
                    $data[$key * 2 + 1]['post_data']     = json_encode($data[$key * 2 + 1]);
                    $data[$key * 2 + 2]['id']            = $item['shop_goods_id'];
                    $data[$key * 2 + 2]['temai_id']      = $item['top_line_article_id'] . $item['shop_goods_id'];
                    $data[$key * 2 + 2]['type']          = '放心购';
                    $data[$key * 2 + 2]['img']           = $item['img_vice'];
                    $data[$key * 2 + 2]['price']         = $item['price'];
                    $data[$key * 2 + 2]['url']           = $item['real_url'];
                    $data[$key * 2 + 2]['title']         = $item['name'];
                    $data[$key * 2 + 2]['describe_info'] = $item['description'] . $item['description_vice'];
                    $data[$key * 2 + 2]['taobao_id']     = $item['shop_goods_id'];
                    $data[$key * 2 + 2]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
                    $data[$key * 2 + 2]['tmall_url']     = $item['real_url'];
                    $data[$key * 2 + 2]['post_data']     = json_encode($data[$key * 2 + 2]);
                    if (isset($cache_data[$item['shop_goods_id']])) {
                        $data[$key * 2 + 1]['is_add'] = 1;
                        $data[$key * 2 + 2]['is_add'] = 1;
                    } else {
                        $data[$key * 2 + 1]['is_add'] = 0;
                        $data[$key * 2 + 2]['is_add'] = 0;
                    }
                }
                $this->assign('data', array_chunk($data, 4));
            }
        }
        $this->assign('cart_count', count($cache_data));
        $this->display();
    }

    /**
     * 微头条商品数据
     */
    public function daTaoKeItemsList() {
        $keyword = I('get.keyword', '', 'trim');
        $sort    = I('get.sort', 0, 'int');
        $where   = array('add_time' => date('Ymd'));
        if ($keyword) {
            $where['title|short_title|desc'] = array('like', "%$keyword%");
        }
        switch ($sort) {
            case 1 :
                $order = "is_send,coupon_price desc";
                break;
            case 2 :
                $order = "is_send,coupon_price asc";
                break;
            case 3 :
                $order = "is_send,commission_rate desc";
                break;
            case 4 :
                $order = "is_send,commission_rate asc";
                break;
            default :
                $order = 'is_send,id desc';
        }
        $data = M('dtk_goods')->where($where)->order($order)->select();
        $this->assign('data', array_chunk($data, 4));
        $this->display();
    }

    /**
     * 新微头条商品数据
     */
    public function weiTopLine() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        if ($shop_goods_id) {
            $where['shop_goods_id'] = $shop_goods_id;
            $db_data                = M('temai_items')->where($where)->group('description')->order('rand()')->limit(5)->select();
            $img_data               = $desc_data = array();
            foreach ($db_data as $val) {
                $img_data[]  = $val['img'];
                $img_data[]  = $val['img_vice'];
                $desc_data[] = $val['description'] . $val['description_vice'];
            }
            $account = array();
            if ($this->group_id == 1) {
                $account = $this->_getTopLineAccount();
            } else {
                $top_line_account_ids = $this->user_info['top_line_account_ids'];
                if ($top_line_account_ids) {
                    $account = M('top_line_account')->where(array('status' => 1, 'id' => array('in', $top_line_account_ids)))->select();
                }
            }
            $this->assign(array('img_data' => $img_data, 'desc_data' => $desc_data, 'source_id' => $this->user_info['source_id'] ? : $this->source_id, 'account' => $account));
        }
        $this->display();
    }


    /**
     * 一键发送
     */
    public function sendWeiTopLine() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $img        = I('post.img', '', 'trim');
        $content    = I('post.content', '', 'trim');
        $account_id = I('post.account_id', 0, 'int');
        if (empty($img)) {
            $this->error('请选择图片！');
        }
        if (count($img) > 9) {
            $this->error('最多支持9张图片！');
        }
        if (empty($content)) {
            $this->error('内容不能为空！');
        }
        if (empty($account_id)) {
            $this->error('请选择发布账号！');
        }
        $cookie = M('top_line_account')->getFieldById($account_id, 'cookie');
        if (empty($cookie)) {
            $this->error('账号cookie不能为空！');
        }
        $img_data = array();
        foreach ($img as $v) {
            $img_res = $this->_uploadWeiTopImg($v, $cookie);
            if ($img_res['status'] == 1) {
                $img_data[] = $img_res['img_key'];
            }
        }
        if (empty($img_data)) {
            $this->error('上传图片失败！');
        }
        $send_url  = "https://mp.toutiao.com/thread/add_thread/";
        $send_data = array(
            'content'               => $content,
            'content_rich_span'     => array('links' => array()),
            'forum_id'              => 6564242300,
            'image_list'            => $img_data,
            'mention_forum_id_list' => array(),
            'mention_user_id_list'  => array(),
        );
        $res       = $this->_weiPost($send_url, json_encode($send_data), $cookie);
        $res       = json_decode($res, true);
        if ($res['message'] == 'success') {
            $this->success('发送成功');
        } else {
            $this->error('发送失败！');
        }
    }

    /**
     * 添加商品
     */
    public function addItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id   = I('post.id', '', 'trim');
        $type      = I('post.type', 0, 'int');
        $save_data = I('post.post_data', '', 'trim');
        if (!$item_id || $save_data == '') {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (count($data) >= 20) {
            $this->error('商品数量最多不能超过20件');
        }
        if (isset($data[$item_id]) && $data[$item_id]) {
            $this->error('该商品已在选品库，不能重复添加');
        }
        $cookie = $this->_getSaleCookie();
        $temp   = $this->_get(sprintf($this->sale_item_info_url, $item_id), array(), $cookie);
        if ($temp === false) {
            $this->error('特卖达人cookie信息已过期！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['errno'] != '0') {
            $this->error($temp_data['msg']);
        }
        if (!$temp_data['goods_infos']) {
            M('temai_items')->where(array('shop_goods_id' => $item_id))->save(array('status' => 0));
            $this->error('商品已下架');
        }
        $fee            = $temp_data['goods_infos'][0]['cos_info']['cos_fee'];
        $ratio          = ($temp_data['goods_infos'][0]['cos_info']['cos_ratio'] * 100) . "%";
        $month_sell_num = $temp_data['goods_infos'][0]['month_sell_num'];
        $cookie         = $this->_getTopLineCookie();
        $temp           = $this->_get($this->item_info_url, array('gurl' => $save_data['tmall_url']), $cookie);
        if ($temp === false) {
            $this->error('请求服务器失败！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['message'] == 'error') {
            $this->error('该账号对应cookie信息已过期，请联系管理员！');
        }
        if (strpos($save_data['img'], 'ttcdn-tos')) {
            $upload_res = $this->_uploadSaleImg($save_data['img'], 'item');
            if ($upload_res['status'] == 0) {
                $this->error($upload_res['info']);
            }
            $save_data['img'] = $upload_res['url'];
            $img_key          = $upload_res['img_key'];
        }
        if (!isset($img_key)) {
            $img_key = explode('?', $save_data['img']);
            $img_key = explode('/', $img_key[0]);
            $img_key = $img_key[count($img_key) - 1];
        }
        $charge_url = $temp_data['data']['charge_url'];
        $pid        = get_word($charge_url, '\?pid=', '&');
        $sub_pid    = get_word($charge_url, '&subPid=', '&');
        if ($pid && $sub_pid) {
            $charge_url = str_replace($pid, $this->pid, $charge_url);
            $charge_url = str_replace($sub_pid, $this->pid, $charge_url);
        }
        if ($type > 0) {
            $obj = new TranslateSDK();
            $res = $obj->translate($save_data['describe_info'], 'zh', 'en');
            $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
            if (!isset($res['trans_result'][0]['dst'])) {
                $this->error('伪原创失败！');
            }
            $save_data['describe_info'] = $res['trans_result'][0]['dst'];

        }
        $url                    = str_replace('http:\/\/', '', $save_data['img']);
        $url                    = str_replace('https:\/\/', '', $url);
        $save_data['json_data'] = array(
            'url'         => $url,
            'uri'         => $img_key,
            'ic_uri'      => $save_data['type'] == '放心购' ? $img_key : '',
            'product'     => array(
                'product_url'        => $save_data['tmall_url'],
                'price_url'          => $charge_url,
                'corrdinate'         => "50%,50%",
                'price'              => $temp_data['data']['price'],//$save_data['price'],原始不对
                'source_type'        => $temp_data['data']['source_type'],
                'title'              => $save_data['title'],
                'recommend_reason'   => $save_data['describe_info'],
                'commodity_id'       => $temp_data['data']['commodity_id'],
                'slave_commodity_id' => $temp_data['data']['slave_commodity_id'],
                'goods_json'         => $temp_data['data']['goods_json'],
            ),
            'desc'        => $save_data['describe_info'],
            'web_uri'     => $img_key,
            'url_pattern' => "{{image_domain}}",
            'gallery_id'  => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
        );

        $save_data['sort'] = count($data) + 1;
        $data[$item_id]    = $save_data;
        S('top_line_item_' . $this->user_id, $data);
        $json_data = array('fee' => $fee, 'ratio' => $ratio, 'month_sell_num' => $month_sell_num, 'msg' => '添加成功');
        $this->success($json_data);
    }

    /**
     * 编辑商品信息
     */
    public function updateItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', '', 'trim');
        $content = I('post.content', '', 'trim');
        $img_key = I('post.img_key', '', 'trim');
        $img_url = I('post.img_url', '', 'trim');
        $sort    = I('post.sort', 0, 'int');
        if ($item_id == 0) {
            $this->error('请求参数不合法！');
        }
        if (!$img_key || !$img_url) {
            $this->error('商品图片不存在，请上传！');
        }
        if (mb_strlen($content) < 5 || mb_strlen($content) > 200) {
            $this->error('推荐理由必须在5-200个字符之间！');
        }

        $data = $this->_getItemCache();
        if (!isset($data[$item_id]) || !$data[$item_id]) {
            $this->error('该商品不在选品库，无法编辑');
        }
        if ($img_key != $data[$item_id]['json_data']['uri']) {
            $data[$item_id]['json_data']['url']     = $img_url;
            $data[$item_id]['json_data']['uri']     = $img_key;
            $data[$item_id]['json_data']['ic_uri']  = $data[$item_id]['json_data']['ic_uri'] ? $img_key : '';
            $data[$item_id]['json_data']['web_uri'] = $img_key;
        }
        $data[$item_id]['img']               = $img_url;
        $data[$item_id]['sort']              = $sort;
        $data[$item_id]['describe_info']     = $content;
        $data[$item_id]['json_data']['desc'] = $content;
        $user_id                             = $this->user_id;
        S('top_line_item_' . $user_id, $data);
        $this->success('编辑成功');
    }

    /**
     * 商品上移或下移
     */
    public function moveItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id       = I('get.id', 0, 'int');
        $other_id = I('get.other_id', 0, 'int');
        $type     = I('get.type', '+', 'trim');
        if (!$id || !$other_id) {
            $this->error('请求参数不合法！');
        }
        $data = $this->_getItemCache();
        if (!isset($data[$id]) || !$data[$id]) {
            $this->error('商品不存在，无法移动！');
        }
        if ($type == '+') {
            $data[$id]['sort']       = $data[$id]['sort'] + 1;
            $data[$other_id]['sort'] = $data[$other_id]['sort'] - 1;
        } else {
            $data[$id]['sort']       = $data[$id]['sort'] - 1;
            $data[$other_id]['sort'] = $data[$other_id]['sort'] + 1;
        }
        S('top_line_item_' . $this->user_id, $data);
        $this->success('移动成功');
    }

    /**
     * 删除选品
     */
    public function delItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', 0, 'trim');
        if ($item_id == 0) {
            $this->error('请求参数不合法！');
        }
        $data = S('top_line_item_' . $this->user_id);
        if (!isset($data[$item_id])) {
            $this->error('该商品不在选品库，不能删除');
        }
        unset($data[$item_id]);
        S('top_line_item_' . $this->user_id, $data);
        $this->success('删除成功');
    }

    /**
     * 预览文章
     */
    public function cartList() {
        $data = $this->_getItemCache();
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->display();
    }

    /**
     * 预览文章
     */
    public function cartSortList() {
        $data = $this->_getItemCache();
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->display();
    }

    /**
     * 设置排序
     */
    public function setItemSort() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_data = I('post.item_data', '', 'trim');
        $data      = $this->_getItemCache();
        $num       = 1;
        foreach ($item_data as $val) {
            if (isset($data[$val]) && $data[$val]) {
                $data[$val]['sort'] = $num;
            }
            $num++;
        }
        S('top_line_item_' . $this->user_id, $data);
        $this->success('操作成功');
    }

    /**
     * 保存文章
     */
    public function saveCart() {
        $data    = $this->_getItemCache();
        $account = array();
        if ($this->group_id == 1) {
            $account = $this->_getTopLineAccount();
        } else {
            $top_line_account_ids = $this->user_info['top_line_account_ids'];
            if ($top_line_account_ids) {
                $account = M('top_line_account')->where(array('status' => 1, 'id' => array('in', $top_line_account_ids)))->select();
            }
        }
        $source_id = session('admin_user_info')['source_id'] ? : 16568122;
        $this->assign('data', $this->_arraySort(array_values($data), 'sort'));
        $this->assign('account', $account);
        $this->assign('source_id', $source_id);
        $this->display();
    }

    /**
     * 保存文章
     */
    public function saveNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $title      = I('post.title', '', 'trim');
        $account_id = I('post.account_id', 0, 'int');
        $img        = I('post.img', '', 'trim');
        $send_time  = I('post.send_time', '', 'trim');
        $source_id  = I('post.source_id', 0, 'int');
        $news_type  = I('post.news_type', 1, 'int');
        if (!$account_id) {
            $this->error('请选择文章的发布账号！');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        if (!$img) {
            $this->error('请选择封面图！');
        }
        $items = $this->_getItemCache();
        if (count($items) == 0) {
            $this->error('商品库暂无商品，请先添加商品！');
        }
        $content = $json_content = $json_covers_img = array();
        foreach ($img as $id) {
            $json_covers_img[] = array(
                'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'url'          => $items[$id]['json_data']['url'],
                'uri'          => $items[$id]['json_data']['uri'],
                'origin_uri'   => $items[$id]['json_data']['uri'],
                'ic_uri'       => $items[$id]['json_data']['ic_uri'],
                'thumb_width'  => 700,
                'thumb_height' => 700,
            );
        }
        $items_data = $this->_arraySort(array_values($items), 'sort');
        foreach ($items_data as $item) {
            $json_content[] = $item['json_data'];
            unset($item['json_data']);
            $content[] = $item;
        }
        $data = array(
            'user_id'         => $this->user_info['id'],
            'account_id'      => $account_id,
            'username'        => $this->user_info['name'],
            'title'           => $title,
            'json_covers_img' => json_encode($json_covers_img),
            'content'         => json_encode($content),
            'json_content'    => json_encode(array('list' => $json_content)),
            'send_time'       => $send_time ? date('Y-m-d H:i', strtotime($send_time)) : 0,
            'add_time'        => time(),
            'news_type'       => $news_type,
            'source_id'       => $source_id == 0 ? session('admin_user_info')['source_id'] : $source_id,
            'source_from_id'  => rand(10000000, 99999999) . '333' . rand(10000000, 99999999)
        );
        $res  = M('top_line_news')->add($data);
        if ($res) {
            $this->_delItemCache();
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 获取缓存商品
     */
    protected function _getItemCache() {
        return S('top_line_item_' . $this->user_id) ? : array();
    }

    /**
     * 删除缓存商品
     */
    protected function _delItemCache() {
        S('top_line_item_' . $this->user_id, null);
    }

    /**
     * @param $file_url
     * @param string $type
     * @return array
     */
    protected function _uploadSaleImg($file_url, $type = 'wei_top') {
        $url_path = dirname(APP_PATH) . '/www/Uploads/temp_' . $this->user_id . '.jpg';
        $file_url = str_replace('https', 'http', $file_url);
        $img_data = getimagesize($file_url);
        /*if ($type == 'item') {
            if ($img_data[0] < 500 || $img_data[1] < 500) {
                return array('status' => 0, 'info' => "图片尺寸必须大于500*500，实际尺寸{$img_data[0]}*{$img_data[1]}！");
            }
        }*/
        $content = file_get_contents($file_url);
        file_put_contents($url_path, $content);
        $obj   = new \CurlFile($url_path);
        $param = array('upfile' => $obj);
        $data  = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
        unlink($url_path);
        if (strtolower($data['state']) == 'success') {
            $url = "https://p2.pstatp.com/large/{$data['original']}";
            return array('status' => 1, 'info' => 'ok', 'url' => $url, 'img_key' => $data['original']);
        } else {
            return array('status' => 0, 'info' => '上传失败！');
        }
    }


    /**
     * 文件上传
     */
    public function uploadTopImg() {
        $upload           = new \Think\Upload();// 实例化上传类
        $upload->maxSize  = 5 * 1024 * 1024;// 设置附件上传大小
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = ROOT_PATH . '/Uploads/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        $info             = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            $url_path = dirname(APP_PATH) . '/www/Uploads/' . $info['file']['savepath'] . $info['file']['savename'];
            $url_path = str_replace('\\', '/', $url_path);
            $obj      = new \CurlFile($url_path);
            $param    = array('upfile' => $obj);
            $data     = json_decode($this->_file($this->upload_url, $param, $this->_getTopLineCookie()), true);
            unlink($url_path);
            if (strtolower($data['state']) == 'success') {
                $url = "https://p2.pstatp.com/large/{$data['original']}";
                $this->success(array('url' => $url, 'img_key' => $data['original']));
            } else {
                $this->error('上传失败');
            }
        }
    }

    /**
     * 头条文章列表
     */
    public function newsList() {
        $user_id = $this->user_id;
        $where   = $user_data = array();
        if ($this->group_id == 1) {
            $user_data = $this->_getUserAllData();
            $user_id   = I('get.user_id', 0, 'int');
            if ($user_id) {
                $where = array('user_id' => $user_id);
            }
        } else {
            $where = array('user_id' => $user_id);
        }
        $count  = M('top_line_news')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('top_line_news')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'page'      => $page->show(),
            'data'      => $data,
            'account'   => $this->_getTopLineAccount(),
            'user_data' => $user_data,
        );
        $this->assign($assign);
        $this->display();
    }


    /**
     * 头条文章列表
     */
    public function albumList() {
        $title   = I('get.title', '', 'trim');
        $is_send = I('get.is_send', '', 'trim');
        $where   = array();
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($is_send !== '') {
            $where['is_send'] = $is_send;
        }
        $count  = M('top_line_album')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('top_line_album')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'page'    => $page->show(),
            'data'    => $data,
            'account' => $this->_getTopLineAccount(),
            'is_send' => $is_send === '' ? 2 : $is_send,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 预览文章
     */
    public function newsInfo() {
        $id   = I('get.id', 0, 'int');
        $info = M('top_line_news')->find($id);
        $data = json_decode($info['content'], true);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 修改文章
     */
    public function updateNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id    = I('post.id', 0, 'int');
        $title = I('post.title', '', 'trim');
        $info  = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        $res = M('top_line_news')->save(array('id' => $id, 'title' => $title));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 发布文章
     */
    public function publish() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', 'item', 'trim');
        $info = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendTop($info, $type);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0) {
            $pgc_id = $res['data']['pgc_id'];
            M('top_line_news')->save(array('id' => $id, 'pgc_id' => $pgc_id, 'is_send' => 1, 'save_type' => $type));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
        }
    }

    /**
     * 存草稿
     */
    public function figure() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', 'item', 'trim');
        $info = M('top_line_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendTop($info, $type, 0);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0 && trim($res['message']) != 'error') {
            $pgc_id = $res['data']['pgc_id'];
            M('top_line_news')->save(array('id' => $id, 'pgc_id' => $pgc_id, 'is_save' => 1, 'save_type' => $type));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
        }
    }

    /**
     * 存草稿
     */
    public function albumFigure() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('top_line_album')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendAlbumTop($info);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0 && trim($res['message']) != 'error') {
            M('top_line_album')->save(array('id' => $id, 'is_send' => 1, 'fail_reason' => $res['message']));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
        }
    }

    /**
     * @param $info
     * @return bool|mixed
     */
    protected function _sendAlbumTop($info) {
        $post_data = array(
            'article_type'           => 0,
            'title'                  => $info['title'],
            'content'                => $info['content'],
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
            'pgc_feed_covers'        => $info['covers'],
            'need_pay'               => 0,
            'from_diagnosis'         => 0,
            'save'                   => 0,
        );
        $res       = $this->_post($this->send_url, $post_data, $this->_getAlbumCookie($info['media_id']));
        return $res;
    }

    /**
     * @param $media_id
     * @return mixed
     */
    protected function _getAlbumCookie($media_id) {
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
     * 删除文章
     */
    public function newsDel() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('get.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $is_send = M('top_line_news')->getFieldById($id, 'is_send');
        if ($is_send == 1) {
            $this->error('该文章已发布，不能删除！');
        }
        $res = M('top_line_news')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @param $info
     * @param $type
     * @param int $save_type
     * @return bool|mixed
     */
    protected function _sendTop($info, $type, $save_type = 1) {
        $news_type = $info['news_type'];
        if ($type == 'item') {
            $items = json_decode($info['json_content'], true);
        } else if ($type == 'img') {
            $items = json_decode($info['json_content'], true);
            foreach ($items['list'] as &$val) {
                if ($news_type == 1) {
                    $val['product'] = new \StdClass();
                } else {

                }

                $val['desc']       = '';
                $val['ic_uri']     = '';
                $val['gallery_id'] = (int)$val['gallery_id'];
            }
            $info['json_content'] = json_encode($items);

        } else {
            $items = json_decode($info['json_content'], true);
            foreach ($items['list'] as &$val) {
                if ($news_type == 1) {
                    $val['product'] = new \StdClass();
                    $val['width']   = '700';
                    $val['height']  = '700';
                } else {

                }
                $val['ic_uri'] = '';
            }
            $info['json_content'] = json_encode($items);
        }
        $gallery_data = $gallery_info = array();
        $account      = $this->_getTopLineAccount();
        foreach ($items['list'] as $item) {
            if ($type == 'item') {
                $gallery_data[$item['gallery_id']] = $item;
                $gallery_info[]                    = $item;
            } else {
                unset($item['product']);
                $gallery_data[$item['gallery_id']] = $item;
                $gallery_info[]                    = $item;
            }

        }
        if ($news_type == 1) {
            $post_data = array(
                'abstract'               => '',
                'authors'                => '',
                'self_appoint'           => 0,
                'save'                   => $save_type,
                'pgc_id'                 => '',
                'urgent_push'            => true,
                'is_draft'               => '',
                'title'                  => $info['title'],
                'content'                => "{!-- PGC_GALLERY:{$info['json_content']} --}",
                'pgc_feed_covers'        => $info['json_covers_img'],
                'gallery_data'           => $gallery_data,
                'gallery_info'           => $gallery_info,
                'article_ad_type'        => 3,
                'recommend_auto_analyse' => 0,
                'ic_uri_list'            => json_decode($info['img_keys']) ? : '',
                'article_type'           => 3,
                'timer_status'           => $info['send_time'] > 0 ? 1 : 0,
                'timer_time'             => $info['send_time'] > 0 ? $info['send_time'] : date('Y-m-d H:i'),
                'from_diagnosis'         => 0,
                'pgc_debut'              => 0
            );

        } else {
            $post_data = array(
                'article_type'           => 0,
                'title'                  => $info['title'],
                'content'                => $this->_createContent($items, $info, $type),
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
                'timer_status'           => $info['send_time'] > 0 ? 1 : 0,
                'timer_time'             => $info['send_time'] > 0 ? $info['send_time'] : date('Y-m-d H:i'),
                'column_chosen'          => 0,
                'pgc_id'                 => '',
                'pgc_feed_covers'        => $info['json_covers_img'],
                'need_pay'               => 0,
                'from_diagnosis'         => 0,
                'save'                   => 0,
            );
        }

        $res = $this->_post($this->send_url, $post_data, $account[$info['account_id']]['cookie']);
        return $res;
    }

    /**
     * 头条文章管理
     */
    public function topNewsList() {
        $news_cate   = $this->item_cate;
        $news_cate[] = array('uid' => '6768100064', 'name' => '放心购精选');
        $uid         = I('get.uid', '', 'trim');
        $read_num    = I('get.read_num') < 0 ? 0 : I('get.read_num');
        $time        = I('get.time', '', 'trim');
        $sort        = I('get.sort', '', 'int');
        $page        = I('get.page', 1, 'int');
        $start_time  = $end_time = '';
        if ($time) {
            list($start_time, $end_time) = explode(' - ', $time);
        }
        $url       = sprintf($this->search_news_url, $uid, $read_num, $start_time, $end_time, $sort, $page);
        $data      = json_decode($this->_get($url), true);
        $is_last   = count($data) == 100 ? true : false;
        $url_param = array('uid' => $uid, 'read_num' => $read_num, 'time' => $time, 'sort' => $sort, 'page' => $page);
        $this->assign(array('cate' => $news_cate, 'data' => $data, 'time' => $time, 'is_last' => $is_last, 'page' => $page, 'url_param' => $url_param));
        $this->display();

    }

    /**
     * 一键发送
     */
    public function sendGoods() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id    = I('post.id', 0, 'int');
        $type  = I('post.type', 'wei_top', 'trim');
        $goods = M('dtk_goods')->find($id);
        if (!$id || !$goods) {
            $this->error('请求参数不合法');
        }
        if ($type == 'wei_top') {
            $res = $this->_sendWeiTopLine($goods);
        } else {
            $res = $this->_sendTopLine($goods);
        }
        if ($res['status'] == 1) {
            $this->success($res['info']);
        } else {
            $this->error($res['info']);
        }
    }

    /**
     * @param $goods
     * @return array
     */
    protected function _sendWeiTopLine($goods) {
        $img_res = $this->_uploadWeiTopImg($goods['pic'], $this->_getWeiTopCookie());
        if ($img_res['status'] == 0) {
            return array('status' => 0, 'info' => $img_res['info']);
        }
        $link_res = $this->_getShortLink($goods['goods_id']);
        if ($link_res['status'] == 0) {
            return array('status' => 0, 'info' => $link_res['info']);
        }
        $send_url  = "https://www.toutiao.com/c/ugc/content/publish/";
        $send_data = array(
            'content'    => "点击此链接{$link_res['url']}\r\n券后【{$goods['coupon_price']}】元 包邮秒杀\r\n{$goods['short_title']}\r\n{$goods['desc']}\r\n更多好货请关注【白菜好货分享】",
            'image_uris' => $img_res['img_key'],
        );
        $res       = $this->_post($send_url, $send_data, $this->_getWeiTopCookie());
        $res       = json_decode($res, true);
        if ($res['message'] == 'success') {
            M('dtk_goods')->where(array('id' => $goods['id']))->setField(array('is_send' => 'Y'));
            return array('status' => 1, 'info' => '发送成功');
        } else {
            return array('status' => 0, 'info' => '发送失败！');
        }
    }

    /**
     * @param $goods
     * @return array
     */
    protected function _sendTopLine($goods) {
        $img_res = $this->_uploadSaleImg($goods['pic'], 'tao_bao_top');
        if ($img_res['status'] == 0) {
            return array('status' => 0, 'info' => $img_res['info']);
        }
        $link_res = $this->_getShortLink($goods['goods_id']);
        if ($link_res['status'] == 0) {
            return array('status' => 0, 'info' => $link_res['info']);
        }
        $img       = $img_res['url'];
        $img_key   = $img_res['img_key'];
        $covers[]  = array(
            'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
            'url'          => $img,
            'uri'          => $img_key,
            'origin_uri'   => $img_key,
            'ic_uri'       => $img_key,
            'thumb_width'  => 800,
            'thumb_height' => 800,
        );
        $title     = $goods['short_title'];
        $desc      = $goods['desc'];
        $content   = '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095490fbc8e2690a" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095490fbc8e2690a" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
        $content   .= '<div class="pgc-img"><img class="" src="' . $img . '" data-ic="false" data-ic-uri="" image_type="1"><p class="pgc-img-caption">领' . $goods['coupon_money'] . '优惠券，券后价：' . $goods['coupon_price'] . '</p></div><p>' . $goods['title'] . '</p>';
        $content   .= '<div class="pgc-img"><img class="" src="https://p.pstatp.com/large/pgc-image/1539420095525f71ec7fed4" data-ic="false" data-ic-uri="" data-height="192" data-width="640" image_type="1" web_uri="pgc-image/1539420095525f71ec7fed4" img_width="640" img_height="192"><p class="pgc-img-caption"></p></div>';
        $click_url = $this->_getShortUrl($link_res['url']);
        $res       = $this->_sendTaoBaoTop($title, $content, json_encode($covers), '8888888888', $click_url);
        if ($res == false) {
            return array('status' => 0, 'info' => '请求服务器失败，请稍后重试！');
        } else {
            $res = json_decode($res, true);
            if ($res['code'] == 0) {
                M('dtk_goods')->save(array('id' => $goods['id'], 'is_auto' => 1));
                return array('status' => 1, 'info' => '发送成功');
            } else {
                return array('status' => 0, 'info' => $res['message']);
            }
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
    protected function _sendTaoBaoTop($title, $content, $covers, $media_id, $click_url = null) {
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
        $res = $this->_post($this->send_url, $post_data, $this->_getAlbumCookie($media_id));
        return $res;
    }

    /*
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
     * @param $file_url
     * @return array
     */
    protected function _uploadWeiTopImg($file_url, $cookie) {
        if (strpos($file_url, 'http') === false) {
            $file_url = "https:" . $file_url;
        }
        $url_path = dirname(APP_PATH) . '/www/Uploads/wei_top_temp.jpg';
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
     * 获取头条默认账号信息
     */
    protected function _getWeiTopCookie() {
        $data = S('wei_top_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(8, 'cookie');
            if ($data) {
                S('wei_top_cookie', $data);
            }
        }
        return $data;
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
     * 打开文章详情
     */
    public function openArticleDetail() {
        $id            = I('get.id', 0, 'int');
        $info          = M('temai_article')->find($id);
        $article_id    = $info['article_id'];
        $location_url  = 'http://www.toutiao.com/a' . $article_id;
        $url           = $info['url'];
        $Http          = new Http();
        $content       = $Http->get($url);
        $title_reg_exp = '%<title>(.*?)</title>%si';
        preg_match($title_reg_exp, $content, $title_match);
        $title = "";
        if (count($title_match) > 0) {
            $title = $title_match[1];
        }
        $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
        preg_match($reg_exp, $content, $match);
        $match_data = json_decode($match[1], true);
        $data       = array();
        if (count($match_data) > 0) {
            foreach ($match_data as $k => $item) {
                if (isset($item['price']) && (strpos($item['real_url'], 'haohuo.snssdk.com') || strpos($item['real_url'], 'haohuo.jinritemai.com'))) {
                    $data[$k] = array(
                        'shop_type_name' => $item['shop_type_name'],
                        'img'            => $item['img'],
                        'title'          => $item['name'],
                        'price'          => $item['price'],
                        'desc'           => $item['description'],
                        'url'            => $item['real_url'],
                        'goods_url'      => 'https://haohuo.snssdk.com/product/detail?id=' . $item['shop_goods_id']
                    );
                }
                if (isset($item['commodity'])) {
                    $data[$k] = array(
                        'shop_type_name' => $match_data[$k - 1]['shop_type_name'],
                        'img'            => $item['location'],
                        'title'          => $match_data[$k - 1]['name'],
                        'price'          => $match_data[$k - 1]['price'],
                        'desc'           => $item['description'],
                        'url'            => $match_data[$k - 1]['real_url'],
                        'goods_url'      => 'https://haohuo.snssdk.com/product/detail?id=' . $match_data[$k - 1]['shop_goods_id']
                    );
                }
            }
        }
        $this->assign('location_url', $location_url);
        $this->assign('title', $title);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _post($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        /*$proxy = $this->_getProxy();
        list($ip, $port) = explode(':', $proxy);
        curl_setopt($oCurl, CURLOPT_PROXY, $ip);
        curl_setopt($oCurl, CURLOPT_PROXYPORT, $port);
        curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式*/
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
        //file_put_contents('/tmp/temai.log', var_export(http_build_query($params), true), FILE_APPEND);
        //exit;
        curl_close($oCurl);
        //file_put_contents('/tmp/temai.log', var_export($sContent, true), FILE_APPEND);
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
     * 商品列表
     */
    public function goodsList() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $media_id      = I('get.media_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $time          = I('get.time', '', 'trim,urldecode');
        $where         = array('status' => 1);
        $query         = "status:'1'";
        $filter        = null;
        if ($shop_goods_id) {
            $query                  .= "AND shop_goods_id:'{$shop_goods_id}'";
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($media_id) {
            $query             .= "AND media_id:'{$media_id}'";
            $where['media_id'] = $media_id;
        }
        if ($keyword) {
            $query                                                             .= " AND keyword:'{$keyword}'";
            $where['top_line_article_title|name|description|description_vice'] = array('like', "%{$keyword}%");
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time                           = strtotime($start_time);
                $end_time                             = strtotime($end_time);
                $filter                               = "top_line_article_behot_time>{$start_time} AND top_line_article_behot_time<{$end_time}";
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($this->openSearchStatus === true) {
            $obj       = new OpenSearch();
            $count     = $obj->searchCount($query, $filter);
            $p         = I('get.p', 1, 'int');
            $page      = $this->pages($count, $this->limit);
            $start_num = ($p - 1) * $this->limit;
            $open_data = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->limit);
            $db_data   = $open_data['data'];
        } else {
            $count   = M('temai_items')->where($where)->count();
            $page    = $this->pages($count, $this->limit);
            $limit   = $page->firstRow . ',' . $page->listRows;
            $db_data = M('temai_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
        }
        $data = array();
        foreach ($db_data as $key => $item) {
            $data[$key * 2 + 1]['id']            = $item['id'];
            $data[$key * 2 + 1]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 1]['type']          = '放心购';
            $data[$key * 2 + 1]['img']           = $item['img'];
            $data[$key * 2 + 1]['price']         = $item['price'];
            $data[$key * 2 + 1]['url']           = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 1]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 1]['title']         = $item['name'];
            $data[$key * 2 + 1]['describe_info'] = $item['description'];
            $data[$key * 2 + 1]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 1]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 1]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 1]['tmall_url']     = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 1]['type']          = 1;
            $data[$key * 2 + 2]['id']            = $item['id'];
            $data[$key * 2 + 2]['temai_id']      = $item['top_line_article_id'];
            $data[$key * 2 + 2]['type']          = '放心购';
            $data[$key * 2 + 2]['img']           = $item['img_vice'];
            $data[$key * 2 + 2]['price']         = $item['price'];
            $data[$key * 2 + 2]['url']           = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 2]['temai_title']   = $item['top_line_article_title'];
            $data[$key * 2 + 2]['title']         = $item['name'];
            $data[$key * 2 + 2]['describe_info'] = $item['description_vice'];
            $data[$key * 2 + 2]['taobao_id']     = $item['shop_goods_id'];
            $data[$key * 2 + 2]['behot_time']    = $item['top_line_article_behot_time'];
            $data[$key * 2 + 2]['temai_id']      = "http://www.toutiao.com/a" . $item['top_line_article_id'];
            $data[$key * 2 + 2]['tmall_url']     = 'https://haohuo.snssdk.com/views/product/item?id=' . $item['shop_goods_id'];
            $data[$key * 2 + 1]['type']          = 2;
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'time' => $time, 'type' => 'goods'));
        $this->display();
    }

    /**
     * 添加机器商品
     */
    public function addGoods() {
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', 1, 'int');
        $info = M('temai_items')->find($id);
        if (empty($info)) {
            $this->assign('error_info', '商品信息不存在！');
        } else {
            if ($type == 1) {
                $count = M('top_line_items')->where(array('md5_desc' => md5($info['shop_goods_id'] . $info['description'])))->count('id');
                if ($count > 0) {
                    $this->assign('error_info', '商品信息不存在！');
                } else {
                    $data = array('id' => $info['id'], 'img' => $info['img'], 'desc' => $info['description']);
                    $this->assign('data', $data);
                }
            } else {
                $count = M('top_line_items')->where(array('md5_desc' => md5($info['shop_goods_id'] . $info['description_vice'])))->count('id');
                if ($count > 0) {
                    $this->assign('error_info', '商品信息不存在！');
                } else {
                    $data = array('id' => $info['id'], 'img' => $info['img'], 'desc' => $info['description']);
                    $this->assign('data', $data);
                }
            }
        }
        $this->display();
    }

    /**
     * 添加头条机器选品商品
     */
    public function addTopLineItems() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $img_url = I('post.img_url', '', 'trim');
        $type_id = I('post.type_id', 0, 'int');
        $label   = I('post.label', '', 'trim');
        $desc    = I('post.desc', '', 'trim');
        $info    = M('temai_items')->find($id);
        if (!$id || !$info) {
            $this->error('请求参数不合法！');
        }
        if (empty($img_url)) {
            $this->error('商品图片不能为空！');
        }
        if (empty($type_id)) {
            $this->error('请选择商品分类！');
        }
        if (empty($label)) {
            $this->error('请输入商品标签！');
        }
        if (empty($desc)) {
            $this->error('商品文案不能为空！');
        }
        $count = M('top_line_items')->where(array('md5_desc' => md5($info['shop_goods_id'] . $desc)))->count('id');
        if ($count > 0) {
            $this->error('商品文案不能重复！');
        }
        $img_res = $this->_uploadSaleImg($img_url, 'item');
        if ($img_res['status'] == 0) {
            $this->error($img_res['info']);
        }
        $data = array(
            'shop_goods_id' => $info['shop_goods_id'],
            'img'           => $img_res['url'],
            'img_key'       => $img_res['img_key'],
            'price'         => $info['price'],
            'article_title' => $info['top_line_article_title'],
            'article_id'    => $info['top_line_article_id'],
            'name'          => $info['name'],
            'product_url'   => 'https://haohuo.snssdk.com/views/product/item?id=' . $info['shop_goods_id'],
            'description'   => $desc,
            'label'         => $label,
            'behot_time'    => $info['top_line_article_behot_time'],
            'type_id'       => $type_id,
            'md5_desc'      => md5($info['shop_goods_id'] . $desc),
        );
        $res  = M('top_line_items')->add($data);
        if ($res) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }


    /**
     * 机器选品
     */
    public function machine() {
        $account = array();
        if ($this->group_id == 1) {
            $account = $this->_getTopLineAccount();
        } else {
            $sale_account_ids = $this->user_info['top_line_account_ids'];
            if ($sale_account_ids) {
                $account = M('top_line_account')->where(array('status' => 1, 'id' => array('in', $sale_account_ids)))->select();
            }
        }
        $this->assign('account', $account);
        $this->display();
    }

    /**
     * 获取商品id
     */
    public function getTopLineItemId() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $keyword = I('get.keyword', '', 'trim');
        $type_id = I('get.type_id', '', 'trim');
        $num     = I('get.num', 0, 'int');
        if ($num < 3) {
            $this->error('商品数量不能小于3条');
        }
        if (empty($keyword) && empty($type_id)) {
            $shop_goods_id = M('top_line_items')->where(array('status' => 1))->getField('shop_goods_id', true);
            if (count($shop_goods_id) < $num) {
                $this->error('商品数量不足' . $num . '条，无法生成商品！');
            }
        } else {
            $where = array('status' => 1);
            if ($keyword) {
                $where['label'] = array('like', "%{$keyword}%");
            }
            if ($type_id) {
                $where['type_id'] = $type_id;
            }
            $shop_goods_id = M('top_line_items')->where($where)->group('shop_goods_id')->getField('shop_goods_id', true);
            if (count($shop_goods_id) < $num) {
                $this->error('总商品数量少于生成商品数量，请更换生成条件！');
            }
        }
        $data = $this->_getGoodId($shop_goods_id, $num);
        $this->success(json_encode($data));
    }

    /**
     * @param $shop_goods_id
     * @param $num
     * @return array
     */
    protected function _getGoodId($shop_goods_id, $num) {
        $data_key = array_rand($shop_goods_id, $num);
        $data     = array();
        foreach ($data_key as $val) {
            $data[] = $shop_goods_id[$val];
        }
        $md5_key = md5($data[0] . $data[1] . $data[2]);
        if (S('md5_top_line' . $md5_key)) {
            $this->_getGoodId($shop_goods_id, $num);
        } else {
            return $data;
        }
    }

    /**
     * 机器选品商品数据组合
     */
    public function getTopLineItem() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id   = I('post.shop_goods_id', '', 'trim');
        $num       = I('post.num', 0, 'int');
        $post_data = M('top_line_items')->where(array('shop_goods_id' => $item_id))->order('rand()')->find();
        if (!$item_id || !$post_data) {
            $this->error('请求参数不合法！');
        }
        $label  = explode('、', $post_data['label']);
        $cookie = $this->_getSaleCookie();
        $temp   = $this->_get(sprintf($this->sale_item_info_url, $item_id), array(), $cookie);
        if ($temp === false) {
            $this->error('特卖达人cookie信息已过期！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['errno'] != '0') {
            $this->error($temp_data['msg']);
        }
        if (!$temp_data['goods_infos']) {
            M('top_line_items')->where(array('shop_goods_id' => $item_id))->save(array('status' => 0));
            $this->error('商品已下架');
        }
        $cookie = $this->_getTopLineCookie();
        $temp   = $this->_get($this->item_info_url, array('gurl' => $post_data['product_url']), $cookie);
        if ($temp === false) {
            $this->error('请求服务器失败！');
        }
        $temp_data = json_decode($temp, true);
        if ($temp_data['message'] == 'error') {
            $this->error('该账号对应cookie信息已过期，请联系管理员！');
        }
        $charge_url = $temp_data['data']['charge_url'];
        $pid        = get_word($charge_url, '\?pid=', '&');
        $sub_pid    = get_word($charge_url, '&subPid=', '&');
        if ($pid && $sub_pid) {
            $charge_url = str_replace($pid, $this->pid, $charge_url);
            $charge_url = str_replace($sub_pid, $this->pid, $charge_url);
        }
        $url       = str_replace('http:\/\/', '', $post_data['img']);
        $url       = str_replace('https:\/\/', '', $url);
        $save_data = array(
            'id'            => $item_id,
            'temai_id'      => "http://www.toutiao.com/a{$post_data['article_id']}",
            'type'          => '放心购',
            'img'           => $post_data['img'],
            'price'         => $temp_data['data']['price'],
            'url'           => $post_data['product_url'],
            'temai_title'   => $post_data['article_title'],
            'title'         => $post_data['name'],
            'describe_info' => $post_data['description'],
            'taobao_id'     => $item_id,
            'behot_time'    => $post_data['behot_time'],
            'tmall_url'     => $post_data['product_url'],
            'json_data'     => array(
                'url'         => $url,
                'uri'         => $post_data['img_key'],
                'ic_uri'      => $post_data['img_key'],
                'product'     => array(
                    'product_url'        => $post_data['product_url'],
                    'price_url'          => $charge_url,
                    'corrdinate'         => "50%,50%",
                    'price'              => $temp_data['data']['price'],
                    'source_type'        => $temp_data['data']['source_type'],
                    'title'              => $post_data['name'],
                    'recommend_reason'   => $post_data['description'],
                    'commodity_id'       => $temp_data['data']['commodity_id'],
                    'slave_commodity_id' => $temp_data['data']['slave_commodity_id'],
                    'goods_json'         => $temp_data['data']['goods_json'],
                ),
                'desc'        => $post_data['description'],
                'web_uri'     => $post_data['img_key'],
                'url_pattern' => "{{image_domain}}",
                'gallery_id'  => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
            ));
        $this->assign('item', $post_data);
        $this->assign('item_info', json_encode($save_data));
        $this->assign('num', $num);
        $this->assign('label', $label);
        $html = $this->fetch();
        $this->success(array('html' => $html, 'img' => $post_data['img']));
    }

    /**
     * 保存文章
     */
    public function saveMachineNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $title      = I('post.title', '', 'trim');
        $account_id = I('post.account_id', 0, 'int');
        $img        = I('post.img', '', 'trim');
        $goods_info = I('post.goods_info', '', 'trim');
        if (!$account_id) {
            $this->error('请选择文章的发布账号！');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        if (!$img) {
            $this->error('请选择封面图！');
        }
        $items = array();
        foreach ($goods_info as $val) {
            $temp               = json_decode($val, true);
            $items[$temp['id']] = $temp;
        }
        if (count($items) == 0) {
            $this->error('商品库暂无商品，请先添加商品！');
        }
        $content = $json_content = $json_covers_img = array();
        foreach ($img as $id) {
            $json_covers_img[] = array(
                'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'url'          => $items[$id]['json_data']['url'],
                'uri'          => $items[$id]['json_data']['uri'],
                'origin_uri'   => $items[$id]['json_data']['uri'],
                'ic_uri'       => $items[$id]['json_data']['ic_uri'],
                'thumb_width'  => 700,
                'thumb_height' => 700,
            );
        }
        foreach ($items as $item) {
            $json_content[] = $item['json_data'];
            unset($item['json_data']);
            $content[] = $item;
        }
        $data = array(
            'user_id'         => $this->user_info['id'],
            'account_id'      => $account_id,
            'username'        => $this->user_info['name'],
            'title'           => $title,
            'json_covers_img' => json_encode($json_covers_img),
            'content'         => json_encode($content),
            'json_content'    => json_encode(array('list' => $json_content)),
            'send_time'       => 0,
            'add_time'        => time(),
            'is_machine'      => 1,
            'source_from_id'  => rand(10000000, 99999999) . '333' . rand(10000000, 99999999),
        );
        $res  = M('top_line_news')->add($data);
        if ($res) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 创建专辑content字段为html
     *发文到头条号
     *
     * @param $json_content
     * @return string
     */
    protected function _createContent($json_content, $info, $type) {
        $send_top_line_config = C('SEND_TOP_LINE_CONFIG');
        $text                 = $send_top_line_config['default']['text'];
        $desc_status          = $send_top_line_config['default']['desc'];
        foreach ($send_top_line_config['custom'] as $v) {
            if (in_array($this->user_id, $v['user_id'])) {
                $text        = $v['text'];
                $desc_status = $v['desc'];
                break;
            }
        }
        $source_from_id = $info['source_from_id'] ? $info['source_from_id'] : rand(10000000, 99999999) . '333' . rand(10000000, 99999999);
        $content        = '';
        if (!is_array($json_content)) {
            return $content;
        }
        foreach ($json_content['list'] as $val) {

            if ($val['url']) {
                $content .= '<div class="pgc-img"><img src="' . $val['url'] . '" data-ic="false" data-height="" data-width="" image_type="1"  ><p class="pgc-img-caption">' . $text . '</p></div>';
            }
            if ($val['product']['title']) {
                $val['product']['product_url'] = str_replace("jinritemai", "snssdk", $val['product']['product_url']);
                $content                       .= '<p><a class="pgc-link"  href="' . $val['product']['product_url'] . '&fxg_req_id=&origin_type=4&origin_id=' . $info['source_id'] . '_' . $source_from_id . '&new_source_type=9&new_source_id=120806&source_type=9&source_id=120806&come_from=0#tt_daymode=1&tt_font=m"  target="_blank">' . $val['product']['title'] . '</a></p>';

            }
            if ($type == 'img_desc' && $desc_status == true) {
                $content .= '<p>' . $val['product']['recommend_reason'] . '</p>';
            }
            if ($type == 'item') {
                $content .= '<p>{!-- PGC_COMMODITY:{"img_url":"' . $val['url'] . '","slave_commodity_id":"' . $val['product']['slave_commodity_id'] . '","commodity_id":"' . $val['product']['commodity_id'] . '","source":"放心购","charge_url":"' . $val['product']['product_url'] . '","price":"' . $val['product']['price'] . '","title":"' . $val['product']['title'] . '","goods_json":"' . $val['product']['goods_json'] . '"} --}</p>';
            }
        }
        return $content;
    }

    protected function _createContent1($json_content, $info, $type) {

        $content = '';
        if (!is_array($json_content)) {
            return $content;
        }
        foreach ($json_content['list'] as $val) {

            if ($val['url']) {
                $content .= '<div class="pgc-img"><a href="' . $val['product']['product_url'] . '.&source_type=4.&source_id=' . $info['source_id'] . '" target="_blank" data-link="' . $val['product']['product_url'] . '.&source_type=4.&source_id=' . $info['source_id'] . '"><img src="' . $val['url'] . '" data-ic="false" data-height="" data-width="" image_type="1"  ></a><p class="pgc-img-caption">如果喜欢本商品单击图片了解</p></div>';
            }
            if ($val['product']['title']) {
                $content .= '<p>' . $val['product']['title'] . '</p>';
            }
            if ($type == 'item') {
                $content .= '<p>{!-- PGC_COMMODITY:{"img_url":"' . $val['url'] . '","slave_commodity_id":"' . $val['product']['slave_commodity_id'] . '","commodity_id":"' . $val['product']['commodity_id'] . '","source":"放心购","charge_url":"' . $val['product']['product_url'] . '","price":"' . $val['product']['price'] . '","title":"' . $val['product']['title'] . '","goods_json":"' . $val['product']['goods_json'] . '"} --}</p>';
            }

        }
        return $content;
    }

    /**
     * 小店选品
     */
    public function shopList() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $media_id      = I('get.media_id', '', 'trim');
        $time          = I('get.time', '', 'trim,urldecode');
        $shop_name     = I('get.shop_name', '', 'trim');
        $where         = array('status' => 1, 'shop_id' => array('in', '1,2,3,4'));
        if ($shop_goods_id) {
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($keyword) {
            $where['top_line_article_title|name|description|description_vice'] = array('like', "%{$keyword}%");
        }
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time                           = strtotime($start_time);
                $end_time                             = strtotime($end_time) + 86399;
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($shop_name) {
            $where['shop_name'] = array('like', "%{$shop_name}%");
        }
        $cache_data = $this->_getShopItemCache();
        $count      = M('shop_items')->where($where)->count();
        $page       = $this->pages($count, $this->limit);
        $limit      = $page->firstRow . ',' . $page->listRows;
        $data       = M('shop_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
        foreach ($data as &$item) {
            if (isset($cache_data[$item['shop_goods_id']])) {
                $item['is_add'] = 1;
            } else {
                $item['is_add'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'cart_count' => count($cache_data), 'time' => $time));
        $this->display();
    }

    /**
     * 获取缓存商品
     */
    protected function _getShopItemCache() {
        return S('shop_item_' . $this->user_id) ? : array();
    }

    /**
     * 删除缓存商品
     */
    protected function _delShopItemCache() {
        S('shop_item_' . $this->user_id, null);
    }

    /**
     * 添加商品
     */
    public function addShopItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', '', 'trim');
        $item_id = I('post.item_id', '', 'trim');
        $info    = M('shop_items')->find($id);
        if (!$item_id || !$id) {
            $this->error('请求参数不合法！');
        }
        if (empty($info)) {
            $this->error('商品信息不存在！');
        }
        $data = $this->_getShopItemCache();
        if (count($data) >= 20) {
            $this->error('商品数量最多不能超过20件');
        }
        if (isset($data[$item_id]) && $data[$item_id]) {
            $this->error('该商品已在选品库，不能重复添加');
        }
        $upload_res = $this->_uploadSaleImg($info['img'], 'shop_item');
        if ($upload_res['status'] == 0) {
            $this->error($upload_res['info']);
        }
        $img            = $upload_res['url'];
        $img_key        = $upload_res['img_key'];
        $save_data      = array(
            'shop_goods_id' => $item_id,
            'url'           => "https://haohuo.snssdk.com/views/product/item?id={$item_id}&tt_project_id=6&origin_type=70&origin_id=441641_{$item_id}",
            'title'         => $info['name'],
            'img'           => $img,
            'img_key'       => $img_key
        );
        $data[$item_id] = $save_data;
        S('shop_item_' . $this->user_id, $data);
        $this->success('添加成功');
    }

    /**
     * 删除选品
     */
    public function delShopItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $item_id = I('post.id', 0, 'trim');
        if ($item_id == 0) {
            $this->error('请求参数不合法！');
        }
        $data = S('shop_item_' . $this->user_id);
        if (!isset($data[$item_id])) {
            $this->error('该商品不在选品库，不能删除');
        }
        unset($data[$item_id]);
        S('shop_item_' . $this->user_id, $data);
        $this->success('删除成功');
    }

    /**
     * 预览文章
     */
    public function cartShopList() {
        $data = $this->_getShopItemCache();
        $this->assign('data', array_values($data));
        $this->display();
    }

    /**
     * 保存文章
     */
    public function saveShopCart() {
        $data    = $this->_getShopItemCache();
        $account = array();
        if ($this->group_id == 1) {
            $account = $this->_getTopLineAccount();
        } else {
            $top_line_account_ids = $this->user_info['top_line_account_ids'];
            if ($top_line_account_ids) {
                $account = M('top_line_account')->where(array('status' => 1, 'id' => array('in', $top_line_account_ids)))->select();
            }
        }
        $this->assign('data', array_values($data));
        $this->assign('account', $account);
        $this->display();
    }


    /**
     * 保存文章
     */
    public function saveShopNews() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $title      = I('post.title', '', 'trim');
        $account_id = I('post.account_id', 0, 'int');
        $img        = I('post.img', '', 'trim');
        $send_time  = I('post.send_time', '', 'trim');
        if (!$account_id) {
            $this->error('请选择文章的发布账号！');
        }
        if (mb_strlen($title) < 5 || mb_strlen($title) > 30) {
            $this->error('文章标题必须是5-30个字符！');
        }
        if (!$img) {
            $this->error('请选择封面图！');
        }
        $items = $this->_getShopItemCache();
        if (count($items) == 0) {
            $this->error('商品库暂无商品，请先添加商品！');
        }
        $json_covers_img = array();
        foreach ($img as $id) {
            $json_covers_img[] = array(
                'id'           => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'url'          => $items[$id]['img'],
                'uri'          => $items[$id]['img_key'],
                'origin_uri'   => $items[$id]['img_key'],
                'ic_uri'       => $items[$id]['img_key'],
                'thumb_width'  => 800,
                'thumb_height' => 800,
            );
        }
        $data = array(
            'user_id'         => $this->user_info['id'],
            'account_id'      => $account_id,
            'username'        => $this->user_info['name'],
            'title'           => $title,
            'json_covers_img' => json_encode($json_covers_img),
            'content'         => json_encode($items),
            'send_time'       => $send_time ? date('Y-m-d H:i', strtotime($send_time)) : 0,
            'add_time'        => time(),
        );
        $res  = M('top_line_shop_news')->add($data);
        if ($res) {
            $this->_delShopItemCache();
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 头条文章列表
     */
    public function ShopNewsList() {
        $user_id = $this->user_id;
        $where   = $user_data = array();
        if ($this->group_id == 1) {
            $user_data = $this->_getUserAllData();
            $user_id   = I('get.user_id', 0, 'int');
            if ($user_id) {
                $where = array('user_id' => $user_id);
            }
        } else {
            $where = array('user_id' => $user_id);
        }
        $count  = M('top_line_shop_news')->where($where)->count();
        $page   = $this->pages($count, $this->limit);
        $limit  = $page->firstRow . ',' . $page->listRows;
        $data   = M('top_line_shop_news')->where($where)->limit($limit)->order('id desc')->select();
        $assign = array(
            'page'      => $page->show(),
            'data'      => $data,
            'account'   => $this->_getTopLineAccount(),
            'user_data' => $user_data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 发布文章
     */
    public function shopFigure() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $type = I('get.type', 'img', 'trim');
        $info = M('top_line_shop_news')->find($id);
        if (!$id || !$info) {
            $this->error('文章不存在');
        }
        $res = $this->_sendShopTop($info, $type);
        if ($res == false) {
            $this->error('请求服务器失败，请稍后重试！');
        }
        $res = json_decode($res, true);
        if ($res['code'] == 0) {
            $pgc_id = $res['data']['pgc_id'];
            M('top_line_shop_news')->save(array('id' => $id, 'pgc_id' => $pgc_id, 'is_send' => 1, 'save_type' => $type));
            $this->success($res['message']);
        } else {
            $this->error($res['message']);
        }
    }

    /**
     * @param $info
     * @param $type
     * @return bool|mixed
     */
    protected function _sendShopTop($info, $type) {
        $post_data = array(
            'article_type'           => 0,
            'title'                  => $info['title'],
            'content'                => $this->_createShopContent($info, $type),
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
            'timer_status'           => $info['send_time'] > 0 ? 1 : 0,
            'timer_time'             => $info['send_time'] > 0 ? $info['send_time'] : date('Y-m-d H:i'),
            'column_chosen'          => 0,
            'pgc_id'                 => '',
            'pgc_feed_covers'        => $info['json_covers_img'],
            'need_pay'               => 0,
            'from_diagnosis'         => 0,
            'save'                   => 0,
        );
        $account   = $this->_getTopLineAccount();
        $res       = $this->_post($this->send_url, $post_data, $account[$info['account_id']]['cookie']);
        return $res;
    }

    /**
     * @param $info
     * @param $type
     * @return string
     */
    protected function _createShopContent($info, $type) {
        $data    = @json_decode($info['content'], true);
        $content = '';
        foreach ($data as $val) {
            if ($type == 'img') {
                $content .= '<div class="pgc-img"><a href="' . $val['url'] . '" target="_blank" data-link="' . $val['url'] . '"><img src="' . $val['img'] . '" data-ic="false" data-height="" data-width="" image_type="1"  ></a><p class="pgc-img-caption">如果喜欢本商品单击图片了解</p></div><p>' . $val['title'] . '</p>';
            } else if ($type == 'text') {
                $content .= '<div class="pgc-img"><img src="' . $val['img'] . '" data-ic="false" data-height="" data-width="" image_type="1"  ><p class="pgc-img-caption">如果喜欢本商品单击图片下面文字了解</p></div><p><a href="' . $val['url'] . '" target="_blank" data-link="' . $val['url'] . '">' . $val['title'] . '</a></p>';
            } else {
                $content .= '<div class="pgc-img"><a href="' . $val['url'] . '" target="_blank" data-link="' . $val['url'] . '"><img src="' . $val['img'] . '" data-ic="false" data-height="" data-width="" image_type="1"  ></a><p class="pgc-img-caption">如果喜欢本商品单击图片或下面文字了解</p></div><p><a href="' . $val['url'] . '" target="_blank" data-link="' . $val['url'] . '">' . $val['title'] . '</a></p>';
            }
            $content .= '<pre spellcheck="false">' . $val['product']['recommend_reason'] . '</pre>';
        }
        return $content;
    }


}