<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/24
 * Time: 16:36
 */

namespace Admin\Controller;

/**
 * 小店管理
 * Class ShopController
 *
 * @package Admin\Controller
 */
class ShopController extends CommonController {


    /**
     * 小店账号管理
     */
    public function index() {
        $data = M('shop')->select();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 保存账号
     */
    public function saveAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $name = I('post.name', '', 'trim');
        if (!$name) {
            $this->error('小店名称不能为空！');
        }
        $data = array('name' => $name);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('shop')->save($data);
        } else {
            $res = M('shop')->add($data);
        }
        if ($res) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }

    }

    /**
     * 删除账号
     */
    public function deleteAccount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id = I('post.id', 0, 'int');
        if (!$id) {
            $this->error('账号不存在！');
        }
        $res = M('shop')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 商品抓取
     */
    public function itemCollection() {
        $name  = M('shop')->getField('id,name');
        $count = M('shop_product')->count('id');
        $this->assign('name', $name);
        $this->assign('count', $count);
        $this->display();
    }

    /**
     * 异步抓取商品
     */
    public function ajaxItemCollection() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $shop_id   = I('post.shop_id', '', 'trim');
        $cos_fee   = I('post.cos_fee', 0, 'int');
        $page      = I('post.page', 1, 'int');
        $shop_name = M('shop')->getFieldById($shop_id, 'name');
        if (empty($shop_name) || empty($shop_id)) {
            $this->error('请选择小店账号！');
        }
        $item_id_arr = array();
        $cookie      = M('sale_account')->getFieldById(1, 'cookie');
        if ($shop_name == '推荐小店') {
            $shop_product = M('shop_product_tuijian');
            $param        = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'keyword_type_shop' => 'word', 'is_recommend' => 1, 'page' => $page - 1);
        } else {
            $shop_product = M('shop_product');
            $param        = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'keyword_type_shop' => 'word', 'keyword_value_shop' => $shop_name, 'page' => $page - 1);
        }
        $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";

        if ($cos_fee > 0) {
            $param['cos_ratio_down'] = $cos_fee;
            $param['cos_ratio_up']   = 100;
        }
        $res = $this->_get($get_url, $param, $cookie);
        if ($res === false) {
            $this->error('您的登录状态已失效，请联系管理员！');
        } else {
            $res = json_decode($res, true);
            if ($res['errno'] === 0) {
                $item_data = $res['goods_infos'];
                if (count($item_data) == 0) {
                    $this->error('success');
                } else {
                    $data = array();
                    foreach ($item_data as $item) {
                        $item_id_arr[]                  = $item['platform_sku_id'];
                        $data[$item['platform_sku_id']] = array(
                            'sku_id'          => $item['sku_id'],
                            'platform_sku_id' => $item['platform_sku_id'],
                            'sku_url'         => $item['sku_url'],
                            'sku_title'       => $item['sku_title'],
                            'sku_price'       => $item['sku_price'],
                            'figure'          => $item['figure'],
                            'shop_name'       => $item['shop_name'],
                            'shop_url'        => $item['shop_url'],
                            'hotrank'         => $item['hotrank'],
                            'month_sell_num'  => $item['month_sell_num'],
                            'cos_fee'         => $item['cos_info']['cos_fee'],
                            'cos_ratio'       => $item['cos_info']['cos_ratio'],
                            'create_time'     => strtotime($item['mtime']),
                            'shop_id'         => $shop_id,
                        );
                    }
                    $item_id_data = array_keys($data);
                    $have_item    = $shop_product->where(array('platform_sku_id' => array('in', $item_id_data)))->getField('platform_sku_id', true);
                    foreach ($have_item as $item_id) {
                        $shop_product->where(array('platform_sku_id' => $item_id))->save($data[$item_id]);
                        unset($data[$item_id]);
                    }
                    if (count($data) > 0) {
                        $shop_product->addAll(array_values($data));
                    }
                    $update_count = count($have_item);
                    $add_count    = count($data);
                    $this->success("第{$page}页订单抓取成功，更新{$update_count}条，添加{$add_count}条!");
                }
            } else {
                $this->error('success');
            }
        }
    }

    /**
     * 商品抓取
     */
    public function shopItemCollection() {
        $name = M('shop')->getField('id,name');
        $data = M('shop_product')->field('count(id) as num,shop_id')->group('shop_id')->select();
        foreach ($data as $val) {
            if (isset($name[$val['shop_id']])) {
                $name[$val['shop_id']] = $name[$val['shop_id']] . '【' . $val['num'] . '】';
            }
        }
        $this->assign('name', $name);
        $this->display();
    }

    /**
     * 异步同步商品
     */
    public function ajaxShopItemCollection() {
        set_time_limit(600);
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $shop_id   = I('post.shop_id', '', 'trim');
        $type      = I('post.type', '', 'trim');
        $time      = I('post.time', '', 'trim,urldecode');
        $shop_name = M('shop')->getFieldById($shop_id, 'name');
        if (empty($shop_name) || empty($shop_id)) {
            $this->error('请选择小店账号！');
        }
        if ($shop_name == '推荐小店') {
            $shop_product = M('shop_product_tuijian');
            $shop_items   = M('shop_items_tuijian');
        } else {
            $shop_product = M('shop_product');
            $shop_items   = M('shop_items');
        }
        $where = array();
        if ($type == 1) {
            if (empty($time)) {
                $this->error('请选择时间范围！');
            }
            list($start_time, $end_time) = explode(' ~ ', $time);
            if (empty($start_time) || empty($end_time)) {
                $this->error('时间格式不符合要求');
            }
            $where = array('top_line_article_behot_time' => array('between', array(strtotime($start_time), strtotime($end_time) + 86399)));
        }
        $add_data      = array();
        $shop_goods_id = $shop_product->where(array('shop_id' => $shop_id))->getField('platform_sku_id', true);
        $item_data     = M('temai_items')->where(array('shop_goods_id' => array('in', $shop_goods_id)))->where($where)->group('description')->select();
        foreach ($item_data as $val) {
            unset($val['id']);
            $id = $shop_items->where(array('md5_desc' => md5($val['shop_goods_id'] . $val['description'])))->getField('id');
            if ($id > 0) {
                $shop_items->where(array('id' => $id))->save($val);
            } else {
                $val['shop_id']   = $shop_id;
                $val['shop_name'] = $shop_name;
                $val['md5_desc']  = md5($val['shop_goods_id'] . $val['description']);
                $add_data[]       = $val;
            }
        }
        $data = array_chunk($add_data, 999);
        foreach ($data as $v) {
            $shop_items->addAll($v);
        }
        $count        = count($item_data);
        $add_count    = count($add_data);
        $update_count = $count - $add_count;
        $this->success("数据同步完成，更新{$update_count}条，添加{$add_count}条!");
    }

    /**
     * 更新小店选品缓存
     */
    public function updateShopItemCache() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $shop_goods_id = M('shop_items')->where(array('status' => 1))->group('shop_goods_id')->getField('shop_goods_id', true);
        S('shop_items_data', $shop_goods_id, 365 * 86400);
        $this->success('更新成功');
    }

    /**
     * 获取小店商品
     */
    public function product() {
        $title         = I('get.title', '', 'trim');
        $shop_name     = I('get.shop_name', '', 'trim');
        $where         = array('create_time' => array('between', array(strtotime('-20 days'), time())));
        $select        = true;
        $shop_goods_id = M('user_items')->where(array('user_id' => $this->user_id, 'create_time' => array('between', array(strtotime(date('Y-m-01')), strtotime(date('Y-m-d')) + 86399))))->getField('shop_goods_id', true);
        if ($this->group_id != 1) {
            if (empty($this->user_info['shop_ids'])) {
                $select = false;
            } else {
                $where['shop_id'] = array('in', $this->user_info['shop_ids']);
            }
        }
        if ($shop_goods_id) {
            $where['platform_sku_id'] = array('not in', $shop_goods_id);
        }
        if ($title) {
            $where['sku_title'] = array('like', "%{$title}%");
        }
        if ($shop_name) {
            $where['shop_name'] = array('like', "%$shop_name%");
        }
        if ($select) {
            $count = M('shop_product')->where($where)->count();
            $page  = $this->pages($count, $this->limit);
            $limit = $page->firstRow . ',' . $page->listRows;
            $data  = M('shop_product')->where($where)->limit($limit)->order('id desc')->select();
            $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'type' => 'shop'));
        }
        $this->display();
    }

    /**
     * 手工添加推荐语
     */
    public function addUserItem() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $info          = M('shop_product')->where(array('platform_sku_id' => $shop_goods_id))->find();
        if (empty($shop_goods_id)) {
            $this->assign('error_info', '请求参数不合法，请刷新重试！');
        }
        if (empty($info['sku_id'])) {
            $this->assign('error_info', '商品信息中缺少sku_id,无法添加推荐语！');
        }
        $cookie = $this->_getSaleCookie();
        //$sku_url  = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityDetail?sku_id={$info['sku_id']}";
        $sku_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityDetail?sku_id=" . $shop_goods_id;


        $sku_info = json_decode($this->_get($sku_url, array(), $cookie), true);
        if ($sku_info['errno'] != '0') {
            $this->assign('error_info', $sku_info['msg']);
        }
        $img_data = $sku_info['commodity']['scroll_imgs'];
        $this->assign('img_data', $img_data);
        $this->assign('goods_id', $shop_goods_id);
        $this->display();
    }

    /**
     * 保存商品推荐语
     */
    public function insertUserItem() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $shop_goods_id    = I('post.shop_goods_id', '', 'trim');
        $media_id         = I('post.media_id', '', 'trim');
        $img              = I('post.img', '', 'trim');
        $description      = I('post.description', '', 'trim');
        $img_vice         = I('post.img_vice', '', 'trim');
        $description_vice = I('post.description_vice', '', 'trim');
        if (empty($shop_goods_id)) {
            $this->error('商品编号不能为空！');
        }
        if (empty($media_id)) {
            $this->error('请选择商品分类！');
        }
        if (empty($img)) {
            $this->error('请设置商品主图！');
        }
        if (empty($description)) {
            $this->error('商品主图推荐语不能为空！');
        }
        if (mb_strlen($description) < 15 || mb_strlen($description) > 100) {
            $this->error('商品主图推荐语必须在15-100字范围内！');
        }
        if (empty($img_vice)) {
            $this->error('请设置商品副图！');
        }
        if (empty($description_vice)) {
            $this->error('商品副图推荐语不能为空！');
        }
        if (mb_strlen($description_vice) < 15 || mb_strlen($description_vice) > 100) {
            $this->error('商品副图推荐语必须在15-100字范围内！');
        }
        $user_items_count = M('user_items')->where(array('description' => $description))->count('id');
        if ($user_items_count > 0) {
            $this->error('商品推荐语不能已存在，请重新编辑！');
        }
        $count = M('shop_items')->where(array('md5_desc' => md5($shop_goods_id . $description)))->count('id');
        if ($count > 0) {
            $this->error('商品推荐语不能重复，请重新编辑！');
        }
        $data = array(
            'user_id'          => $this->user_id,
            'user_name'        => $this->user_info['name'],
            'shop_goods_id'    => $shop_goods_id,
            'media_id'         => $media_id,
            'img'              => $img,
            'description'      => $description,
            'img_vice'         => $img_vice,
            'description_vice' => $description_vice,
            'create_time'      => time(),
        );
        $res  = M('user_items')->add($data);
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 编辑推荐语
     */
    public function updateUserItem() {
        $id   = I('get.id', 0, 'int');
        $info = M('user_items')->find($id);
        if (empty($id) || empty($info)) {
            $this->assign('error_info', '请求参数不完整，请刷新重试！');
        } else {
            $this->assign('info', $info);
        }
        $this->display();
    }

    /**
     * 保存推荐语
     */
    public function saveUserItem() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id               = I('post.id', 0, 'int');
        $description      = I('post.description', '', 'trim');
        $description_vice = I('post.description_vice', '', 'trim');
        $info             = M('user_items')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('请求参数不合法！');
        }
        if (empty($description)) {
            $this->error('商品主图推荐语不能为空！');
        }
        if (mb_strlen($description) < 15 || mb_strlen($description) > 100) {
            $this->error('商品主图推荐语必须在15-100字范围内！');
        }
        if (empty($description_vice)) {
            $this->error('商品副图推荐语不能为空！');
        }
        if (mb_strlen($description_vice) < 15 || mb_strlen($description_vice) > 100) {
            $this->error('商品副图推荐语必须在15-100字范围内！');
        }
        $user_items_count = M('user_items')->where(array('description' => $description))->count('id');
        if ($user_items_count > 0) {
            $this->error('商品推荐语不能已存在，请重新编辑！');
        }
        $count = M('shop_items')->where(array('md5_desc' => md5($info['shop_goods_id'] . $description)))->count('id');
        if ($count > 0) {
            $this->error('商品推荐语不能重复，请重新编辑！');
        }
        $data = array(
            'id'               => $id,
            'description'      => $description,
            'description_vice' => $description_vice,
            'status'           => 0
        );
        $res  = M('user_items')->save($data);
        if ($res) {
            $this->success('编辑成功');
        } else {
            $this->error('请修改后再保存！');
        }
    }

    /**
     * 手写推荐语列表
     */
    public function userItems() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $keyword       = I('get.keyword', '', 'trim');
        $where         = array('user_id' => $this->user_id);
        if ($shop_goods_id) {
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($keyword) {
            $where['description|description_vice'] = array('like', "%{$keyword}%");
        }
        $count = M('user_items')->where($where)->count();
        $page  = $this->pages($count, $this->limit);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('user_items')->where($where)->limit($limit)->order('status asc,id desc')->select();
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'type' => 'user_items'));
        $this->display();
    }

    /**
     * 推荐语审核
     */
    public function userItemsList() {
        $user_id = I('get.user_id', '', 'trim');
        $where   = array('status' => 0);
        if ($user_id != '') {
            $where['user_id'] = $user_id;
        }
        $count = M('user_items')->where($where)->count();
        $page  = $this->pages($count, $this->limit);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('user_items')->where($where)->limit($limit)->order('status asc,id desc')->select();
        $user  = M('tmuser')->getField('id,name');
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'type' => 'user_items_status', 'user' => $user));
        $this->display();
    }

    /**
     * 审核推荐语
     */
    public function updateUserItemStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id           = I('post.id', 0, 'int');
        $status       = I('post.status', 0, 'int');
        $info         = M('user_items')->find($id);
        $shop_product = M('shop_product')->where(array('platform_sku_id' => $info['shop_goods_id']))->find();
        if (empty($id) || empty($status) || empty($info) || empty($shop_product)) {
            $this->error('请求参数不完整，请刷新重试！');
        }
        $res = M('user_items')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            if ($status == 2) {
                $this->_saveShopItems($info, $shop_product);
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * @param $info
     * @param $shop_product
     */
    protected function _saveShopItems($info, $shop_product) {
        $position = array(
            '26.0563380%,33.6842105%',
            '42.2535211%,48.9436619%',
            '68.30986%,38.3802816%',
            '71.126761%,89.7887323%',
            '50.704226%,45.0704225%',
            '-57.042253%,51.7605633%',
            '69.71831%,36.6197183%',
            '50%,53.8732394%',
            '41.9014084%,60.2112676%',
            '35.9154929%,58.9473684%',
        );
        $num      = rand(0, 9);
        $data     = array(
            'top_line_article_id'         => 8888888888,
            'create_user_id'              => 0,
            'top_line_article_title'      => '手工编写推荐语',
            'top_line_article_behot_time' => time(),
            'shop_goods_id'               => $info['shop_goods_id'],
            'price'                       => $shop_product['sku_price'],
            'price_tag_position'          => $position[$num],
            'name'                        => $shop_product['sku_title'],
            'img'                         => $info['img'],
            'description'                 => $info['description'],
            'img_vice'                    => $info['img_vice'],
            'description_vice'            => $info['description_vice'],
            'media_id'                    => $info['media_id'],
            'shop_id'                     => $shop_product['shop_id'],
            'shop_name'                   => $shop_product['shop_name'],
            'md5_desc'                    => md5($info['shop_goods_id'] . $info['description']),
        );
        M('shop_items')->add($data);
    }


}