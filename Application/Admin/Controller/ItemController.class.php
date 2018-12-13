<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/12
 * Time: 9:43
 */

namespace Admin\Controller;

use Common\Org\OpenSearch;

/**
 * Class ItemController
 *
 * @package Admin\Controller
 */
class ItemController extends CommonController {

    /**
     * 特卖商品
     */
    public function saleList() {
        $title          = I('get.title', '', 'trim');
        $shop_goods_id  = I('get.shop_goods_id', '', 'trim');
        $shop_name      = I('get.shop_name', '', 'trim');
        $sort           = I('get.sort', '', 'trim');
        $order          = I('get.order', '', 'trim');
        $cos_ratio_up   = I('get.cos_ratio_up', '', 'trim');
        $cos_ratio_down = I('get.cos_ratio_down', '', 'trim');
        $sku_price_up   = I('get.sku_price_up', '', 'trim');
        $sku_price_down = I('get.sku_price_down', '', 'trim');
        $time           = I('get.time', '', 'trim');
        $where          = array();
        if ($title) {
            $where['sku_title'] = array('like', "%{$title}%");
        }
        if ($shop_goods_id) {
            $where['platform_sku_id'] = $shop_goods_id;
        }
        if ($shop_name) {
            $where['shop_name'] = array('like', "%$shop_name%");
        }
        if ($cos_ratio_up && $cos_ratio_down) {
            $where['cos_ratio'] = array('between', array($cos_ratio_down / 100, $cos_ratio_up / 100));
        }
        if ($sku_price_up && $sku_price_down) {
            $where['sku_price'] = array('between', array($sku_price_down, $sku_price_up));
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time           = strtotime($start_time);
                $end_time             = strtotime($end_time) + 86399;
                $where['create_time'] = array('between', array($start_time, $end_time));
            }
        }
        if ($order && $sort) {
            $order_sort = "{$sort} {$order},create_time desc";
        } else {
            $order_sort = 'create_time desc,id desc';
        }
        $count       = M('product')->where($where)->count();
        $page        = $this->pages($count, $this->limit);
        $limit       = $page->firstRow . ',' . $page->listRows;
        $data        = M('product')->where($where)->limit($limit)->order($order_sort)->select();
        $reward_data = M('reward_product')->where(array('status' => 1))->getField('shop_goods_id', true);
        $shop_data   = M('temai_items')->where(array('type' => 1))->group('shop_goods_id')->getField('shop_goods_id', true);
        $start_num   = strtotime('-3 days');
        $end_num     = time();
        foreach ($data as &$v) {
            if (in_array($v['platform_sku_id'], $reward_data)) {
                $v['is_have'] = 1;
            } else {
                $v['is_have'] = 0;
            }
            if (in_array($v['platform_sku_id'], $shop_data)) {
                $v['is_shop'] = 1;
            } else {
                $v['is_shop'] = 0;
            }
            if ($v['create_time'] >= $start_num && $v['create_time'] <= $end_num) {
                $v['is_new'] = 1;
            } else {
                $v['is_new'] = 0;
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'param' => I('get.'), 'type' => 'sale'));
        $this->display();
    }

    /**
     * 异步采集商品
     */
    public function ajaxCollectionItem() {
        if (!IS_AJAX) {
            $this->error(array('code' => -4, 'info' => '非法请求!'));
        }
        $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
        $cookie  = $this->_getSaleCookie();
        $page    = I('get.page', 1, 'int');
        $param   = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'page' => $page);
        $res     = $this->_get($get_url, $param, $cookie);
        if ($res === false) {
            $this->error(array('code' => -3, 'info' => '您的登录状态已失效，请联系管理员!'));
        } else {
            $res = json_decode($res, true);
            if ($res['errno'] === 0) {
                $item_data = $res['goods_infos'];
                if (count($item_data) == 0) {
                    $this->error(array('code' => 0, 'info' => '数据已加载完成!'));
                }
                $model = M();
                $model->startTrans();
                $data = array();
                foreach ($item_data as $item) {
                    $data[$item['platform_sku_id']] = array(
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
                    );
                }
                $have_data = M('product')->field('id,platform_sku_id')->where(array('platform_sku_id' => array('in', array_keys($data))))->select();
                try {
                    foreach ($have_data as $val) {
                        unset($data[$val['platform_sku_id']]['create_time']);
                        M('product')->where(array('id' => $val['id']))->save($val);
                        unset($data[$val['platform_sku_id']]);
                    }
                    if ($data) {
                        M('product')->addAll(array_values($data));
                    }
                    $model->commit();
                    if ($data) {
                        $this->success(array('code' => 1, 'info' => "第{$page}页商品数据采集成功！"));
                    } else {
                        $this->success(array('code' => 1, 'info' => "第{$page}页商品数据已采集，无新商品！"));
                    }
                } catch (\Exception $e) {
                    $model->rollback();
                    $this->error(array('code' => -2, 'info' => $e->getMessage()));
                }
            } else {
                $this->error(array('code' => -1, 'info' => $res['msg']));
            }
        }
    }

    /**
     * 添加至小店商品
     */
    public function editShop() {
        if (!IS_AJAX) {
            $this->error('非法请求!');
        }
        $shop_goods_id = I('post.shop_goods_id', '', 'trim');
        $type          = I('post.type', 0, 'int');
        $info          = M('temai_items')->where(array('shop_goods_id' => $shop_goods_id))->find();
        if (!$shop_goods_id || !$info) {
            $this->error('商品信息不存在，无法添加至小店商品！');
        }
        if ($type == 1) {
            $db_type = M('temai_items')->where(array('shop_goods_id' => $shop_goods_id))->getField('type');
            if ($db_type > 0) {
                $this->error('该商品已添加至小店商品，不能重复操作！');
            }
        }
        $res = M('temai_items')->where(array('shop_goods_id' => $shop_goods_id))->save(array('type' => $type));
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * 添加奖励库
     */
    public function addReward() {
        $shop_goods_id = I('post.shop_goods_id', '', 'trim');
        $reward_money  = I('post.reward_money', '', 'trim');
        $info          = M('product')->where(array('platform_sku_id' => $shop_goods_id))->find();
        if (!$shop_goods_id || !$info) {
            $this->error('商品信息不存在，无法加入奖励库！');
        }
        $count = M('reward_product')->where(array('shop_goods_id' => $shop_goods_id))->count();
        if ($count > 0) {
            $this->error('该商品已添加至奖励库，不能重复加入！');
        }
        if (!$reward_money || $reward_money <= 0) {
            $this->error('奖励金额不能为空或小于0');
        }
        $data = array('shop_goods_id' => $shop_goods_id, 'reward_money' => $reward_money, 'cos_ratio' => $info['cos_ratio'], 'cos_fee' => $info['cos_fee'], 'add_time' => time());
        $res  = M('reward_product')->add($data);
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 删除奖励库
     */
    public function delReward() {
        $shop_goods_id = I('post.shop_goods_id', '', 'trim');
        $count         = M('reward_product')->where(array('shop_goods_id' => $shop_goods_id))->count();
        if ($count == 0) {
            $this->error('该商品不在奖励库，不能删除！');
        }
        $res = M('reward_product')->where(array('shop_goods_id' => $shop_goods_id))->save(array('status' => 0));
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 奖励选品
     */
    public function rewardList() {
        $cache_data         = $this->_getItemCache();
        $shop_goods_id      = I('get.shop_goods_id', '', 'trim');
        $keyword            = I('get.keyword', '', 'trim');
        $media_id           = I('get.media_id', '', 'trim');
        $reward_data        = M('reward_product')->where(array('status' => 1))->index('shop_goods_id')->select();
        $shop_goods_id_data = array_keys($reward_data);
        if ($shop_goods_id_data) {
            $select = true;
        } else {
            $select = false;
        }
        $where  = array('status' => 1);
        $query  = "status:'1'";
        $filter = null;
        if ($shop_goods_id) {
            if (in_array($shop_goods_id, $shop_goods_id_data)) {
                $query                  .= "AND shop_goods_id:'{$shop_goods_id}'";
                $where['shop_goods_id'] = $shop_goods_id;
            } else {
                $select = false;
            }
        } else {
            $query                  .= " AND shop_goods_id:'" . implode("' OR shop_goods_id:'", $shop_goods_id_data) . "'";
            $where['shop_goods_id'] = array('in', $shop_goods_id_data);
        }
        if ($select == true) {
            if ($keyword) {
                $query                                                             .= " AND keyword:'{$keyword}'";
                $where['top_line_article_title|name|description|description_vice'] = array('like', "%{$keyword}%");
            }
            if ($media_id) {
                $query             .= " AND media_id:'{$media_id}'";
                $where['media_id'] = $media_id;
            }
            $this->_getLastId($where, $filter);
            if ($this->openSearchStatus === true && count($shop_goods_id_data) < 25) {
                $obj       = new OpenSearch();
                $count     = $obj->searchCount($query, $filter);
                $p         = I('get.p', 1, 'int');
                $page      = $this->pages($count, $this->limit);
                $start_num = ($p - 1) * $this->limit;
                $open_data = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->limit);
                $data      = $open_data['data'];
            } else {
                $count = M('temai_items')->where($where)->count();
                $page  = $this->pages($count, $this->limit);
                $limit = $page->firstRow . ',' . $page->listRows;
                $data  = M('temai_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
            }
            foreach ($data as &$item) {
                $json_data            = array(
                    'name'               => $item['name'],
                    'img'                => $item['img'],
                    'img_vice'           => $item['img_vice'],
                    'description'        => $item['description'],
                    'description_vice'   => $item['description_vice'],
                    'price_tag_position' => $item['price_tag_position'],
                    'self_charging_url'  => $item['self_charging_url'],
                );
                $item['post_data']    = json_encode($json_data);
                $item['reward_money'] = $reward_data[$item['shop_goods_id']]['reward_money'];
                $item['cos_fee']      = $reward_data[$item['shop_goods_id']]['cos_fee'];
                $item['cos_ratio']    = $reward_data[$item['shop_goods_id']]['cos_ratio'];
                if (isset($cache_data[$item['shop_goods_id']])) {
                    $item['is_add'] = 1;
                } else {
                    $item['is_add'] = 0;
                }
            }
            $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show()));
        }
        $this->assign(array('cart_count' => count($cache_data)));
        $this->display();
    }

    /**
     * 奖励选品
     */
    public function rewardItem() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $where         = array('status' => 1);
        if ($shop_goods_id) {
            $where['r.shop_goods_id'] = $shop_goods_id;
        }
        $count = M('reward_product')->alias('r')->where($where)->count();
        $page  = $this->pages($count, $this->limit);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('reward_product')->alias('r')->join('left join ytt_product p ON p.platform_sku_id = r.shop_goods_id ')->where($where)->limit($limit)->order('r.add_time desc')->select();
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'type' => 'reward'));
        $this->display();
    }

    /**
     * 商品统计
     */
    public function count() {
        $cache_data = $this->_getItemCache();
        $start_time = I('get.start_time', '', 'trim,urldecode');
        $end_time   = I('get.end_time', '', 'trim,urldecode');
        $media_id   = I('get.media_id', '', 'trim');
        $where      = array('status' => 1);
        $query      = "status:'1'";
        $filter     = null;
        if ($start_time && $end_time) {
            $start_time = strtotime($start_time);
            $end_time   = strtotime($end_time) + 86399;
            if ($end_time - $start_time > 7 * 86400) {
                $this->assign('info', '日期范围起止日期不能超过7天！');
            } else {
                $filter                               = "top_line_article_behot_time>{$start_time} AND top_line_article_behot_time<{$end_time}";
                $where['top_line_article_behot_time'] = array('between', array($start_time, $end_time));
                if ($media_id) {
                    $query             .= " AND media_id:'{$media_id}'";
                    $where['media_id'] = $media_id;
                }
                $this->_getLastId($where, $filter);
                if ($this->openSearchStatus === true && false) {
                    $obj       = new OpenSearch();
                    $distinct  = array('dist_key' => 'shop_goods_id');
                    $count     = $obj->searchCount($query, $filter, $distinct);
                    $p         = I('get.p', 1, 'int');
                    $page      = $this->pages($count, $this->limit);
                    $start_num = ($p - 1) * $this->limit;
                    $open_data = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->limit, $distinct);
                    $data      = $open_data['data'];
                } else {
                    $shop_goods_id = M('temai_items')->where($where)->group('shop_goods_id')->getField('shop_goods_id', true);
                    $count         = count($shop_goods_id);
                    $page          = $this->pages($count, $this->limit);
                    $limit         = $page->firstRow . ',' . $page->listRows;
                    $data          = M('temai_items')->field('*,count(id) as num')->where($where)->limit($limit)->order('num desc')->group('shop_goods_id')->select();
                }
                foreach ($data as &$item) {
                    $json_data         = array(
                        'name'               => $item['name'],
                        'img'                => $item['img'],
                        'img_vice'           => $item['img_vice'],
                        'description'        => $item['description'],
                        'description_vice'   => $item['description_vice'],
                        'price_tag_position' => $item['price_tag_position'],
                        'self_charging_url'  => $item['self_charging_url'],
                    );
                    $item['post_data'] = json_encode($json_data);
                    if (isset($cache_data[$item['shop_goods_id']])) {
                        $item['is_add'] = 1;
                    } else {
                        $item['is_add'] = 0;
                    }
                }
                $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show()));
            }
        }
        $this->assign('cart_count', count($cache_data));
        $this->display();
    }

    /**
     * 奖励选品
     */
    public function commissionList() {
        $cache_data = $this->_getItemCache();
        $keyword    = I('get.keyword', '', 'trim');
        if ($keyword) {
            $where           = array('sku_title' => array('like', "%{$keyword}%"), 'cos_ratio' => array('egt', 0.25));
            $commission_data = M('product')->field('platform_sku_id,cos_fee,cos_ratio')->where($where)->index('platform_sku_id')->select();
            if ($commission_data) {
                $shop_goods_id          = array_keys($commission_data);
                $where                  = array('status' => 1);
                $query                  = "status:'1'";
                $filter                 = null;
                $query                  .= " AND shop_goods_id:'" . implode("' OR shop_goods_id:'", $shop_goods_id) . "'";
                $where['shop_goods_id'] = array('in', $shop_goods_id);
                $this->_getLastId($where, $filter);
                if ($this->openSearchStatus === true && count($shop_goods_id) < 25) {
                    $obj       = new OpenSearch();
                    $count     = $obj->searchCount($query, $filter);
                    $p         = I('get.p', 1, 'int');
                    $page      = $this->pages($count, $this->limit);
                    $start_num = ($p - 1) * $this->limit;
                    $open_data = $obj->search($query, array(array('key' => 'top_line_article_behot_time', 'val' => 0)), $filter, $start_num, $this->limit);
                    $data      = $open_data['data'];
                } else {
                    $count = M('temai_items')->where($where)->count();
                    $page  = $this->pages($count, $this->limit);
                    $limit = $page->firstRow . ',' . $page->listRows;
                    $data  = M('temai_items')->where($where)->limit($limit)->order('top_line_article_behot_time desc')->select();
                }
                foreach ($data as &$item) {
                    $json_data         = array(
                        'name'               => $item['name'],
                        'img'                => $item['img'],
                        'img_vice'           => $item['img_vice'],
                        'description'        => $item['description'],
                        'description_vice'   => $item['description_vice'],
                        'price_tag_position' => $item['price_tag_position'],
                        'self_charging_url'  => $item['self_charging_url'],
                    );
                    $item['post_data'] = json_encode($json_data);
                    $item['cos_fee']   = $commission_data[$item['shop_goods_id']]['cos_fee'];
                    $item['cos_ratio'] = $commission_data[$item['shop_goods_id']]['cos_ratio'];
                    if (isset($cache_data[$item['shop_goods_id']])) {
                        $item['is_add'] = 1;
                    } else {
                        $item['is_add'] = 0;
                    }
                }
                $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show()));
            }
        }
        $this->assign(array('cart_count' => count($cache_data)));
        $this->display();
    }

    /**
     * 更新昨天最后一条商品ID
     */
    public function updateItemId() {
        $time = strtotime('-2 days');
        $id   = M('temai_items')->where(array('top_line_article_behot_time' => array('lt', $time)))->order('id desc')->getField('id');
        S('temai_item_last_id', $id);
    }

    /**
     * 订单商品列表
     */
    public function orderItemsList() {
        $time = I('get.time', '', 'trim,urldecode');
        if (empty($time)) {
            $time = date('Y-m-d', strtotime('-7 days')) . " ~ " . date('Y-m-d');
        }
        $where = array();
        list($start_time, $end_time) = explode(' ~ ', $time);
        if ($start_time && $end_time) {
            $start_time                        = strtotime($start_time);
            $end_time                          = strtotime($end_time) + 86399;
            $where['UNIX_TIMESTAMP(pay_time)'] = array('between', array($start_time, $end_time));
        }
        $goods_id = M('fxg')->where($where)->group('goods_id')->getField('goods_id', true);
        $count    = count($goods_id);
        $page     = $this->pages($count, $this->limit);
        $limit    = $page->firstRow . ',' . $page->listRows;
        $data     = M('fxg')->field('count(goods_id) as num,commodity_info,goods_id,order_money,income,profit_percent,commodity_name,pay_time')->where($where)->limit($limit)->order('num desc')->group('goods_id')->select();
        $img_data = M('product')->where(array('platform_sku_id' => array('in', $goods_id)))->getField('platform_sku_id,figure');
        foreach ($data as &$val) {
            if ($img_data[$val['goods_id']]) {
                $val['img'] = $img_data[$val['goods_id']];
            } else {
                $val['img'] = C('TMPL_PARSE_STRING.__IMAGE_PATH__') . "/default-item.jpg";
            }
        }
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'time' => $time));
        $this->display();
    }

    /**
     * 商品首图
     */
    public function topImg() {
        $title         = I('get.title', '', 'trim');
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $media_id      = I('get.media_id', '', 'trim');
        $time          = I('get.time', '', 'trim,urldecode');
        $where         = array();
        if ($title) {
            $where['title'] = array('like', "%{$title}%");
        }
        if ($shop_goods_id) {
            $where['shop_goods_id'] = $shop_goods_id;
        }
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time    = strtotime($start_time);
                $end_time      = strtotime($end_time) + 86399;
                $where['time'] = array('between', array($start_time, $end_time));
            }
        }
        $count = M('high_item_img')->where($where)->count();
        $page  = $this->pages($count, 50);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('high_item_img')->where($where)->limit($limit)->order('time desc,id desc')->select();
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show(), 'param' => I('get.')));
        $this->display();
    }

    /**
     * 商品收藏
     */
    public function collect() {
        $shop_goods_id = I('get.shop_goods_id', '', 'trim');
        $count         = M('user_product')->where(array('shop_goods_id' => $shop_goods_id, 'user_id' => $this->user_id))->count('id');
        if ($count > 0) {
            $this->assign('error_info', '该商品已加入收藏，不能重复添加');
        } else {
            if ($shop_goods_id) {
                $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
                $cookie  = $this->_getSaleCookie();
                $param   = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'keyword_type' => 'platform_sku_id', 'keyword_value' => $shop_goods_id);
                $res     = $this->_get($get_url, $param, $cookie);
                if ($res === false) {
                    $this->assign('error_info', '您的登录状态已失效，请联系管理员!');
                } else {
                    $res = json_decode($res, true);
                    if ($res['errno'] === 0) {
                        $item_data = $res['goods_infos'];
                        if ($item_data) {
                            $data = array();
                            foreach ($item_data as $item) {
                                $data = array(
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
                                );
                            }
                            if ($data['cos_ratio'] < 0.15) {
                                $cos_ratio = $data['cos_ratio'] * 100;
                                $this->assign('error_info', '商品佣金比率必须大于15%,当前商品佣金比率' . $cos_ratio . '%');
                            } else {
                                $this->assign('data', $data);
                            }
                        } else {
                            $this->assign('error_info', '商品已下架');
                        }
                    } else {
                        $this->assign('error_info', $res['msg']);
                    }
                }
            }
        }
        $this->display();
    }

    /**
     * 加入收藏
     */
    public function insertCollect() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $shop_goods_id = I('post.shop_goods_id', '', 'trim');
        $url           = I('post.url', '', 'trim');
        $title         = I('post.title', '', 'trim');
        $img           = I('post.img', '', 'trim');
        $user_id       = $this->user_id;
        if (empty($shop_goods_id)) {
            $this->error('商品编号不能为空');
        }
        if (empty($img)) {
            $this->error('商品图片不能为空');
        }
        if (empty($title)) {
            $this->error('商品标题不能为空');
        }
        if (empty($url)) {
            $this->error('商品链接不能为空');
        }
        $count = M('user_product')->where(array('shop_goods_id' => $shop_goods_id, 'user_id' => $user_id))->count('id');
        if ($count > 0) {
            $this->error('该商品已加入收藏，不能重复添加');
        }
        $res = M('user_product')->add(array('shop_goods_id' => $shop_goods_id, 'img' => $img, 'url' => $url, 'title' => $title, 'user_id' => $user_id, 'user_name' => $this->user_info['name'], 'add_time' => time()));
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    /**
     * 我的收藏
     */
    public function userCollect() {
        $group_id = $this->group_id;
        $where    = array();
        if ($group_id == 1) {
            $where['status'] = 0;
        } else {
            $where['user_id'] = $this->user_id;
        }
        $count = M('user_product')->where($where)->count();
        $page  = $this->pages($count, 50);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = M('user_product')->where($where)->limit($limit)->order('add_time desc')->select();
        $this->assign(array('data' => array_chunk($data, 4), 'page' => $page->show()));
        $this->display();
    }

    /**
     * 删除收藏
     */
    public function delUserProduct() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('post.id', 0, 'int');
        $status = I('post.status', 0, 'int');
        $info   = M('user_product')->find($id);
        if (empty($id) || empty($status) || empty($info)) {
            $this->error('请求参数不完整，请刷新重试！');
        }
        $res = M('user_product')->where(array('id' => $id))->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 审核收藏
     */
    public function updateUserProductStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('post.id', 0, 'int');
        $status = I('post.status', 0, 'int');
        $info   = M('user_product')->find($id);
        if (empty($id) || empty($status) || empty($info)) {
            $this->error('请求参数不完整，请刷新重试！');
        }
        $res = M('user_product')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * 公共收藏
     */
    public function publicCollect() {

    }
}