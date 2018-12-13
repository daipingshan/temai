<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/5
 * Time: 10:00
 */

namespace Data\Controller;

/**
 * 商品采集器
 * Class ItemController
 *
 * @package Data\Controller
 */
class ItemsController extends CommonController {

    /**
     * 抓取商品数据
     */
    public function getItems() {
        $get_time = time();
        $is_have  = true;
        while ($is_have) {
            if (time() - $get_time > 3000) {
                $is_have = false;
            }
            $db_data = M('temai_article')->field('id,article_id,title,url,create_user_id,behot_time,media_id')->where(array('is_add_item' => 0, 'article_genre' => 1, 'is_normal' => 1))->index('id')->order('behot_time desc,id desc')->limit(100)->select();
            if (count($db_data) < 100) {
                $this->_addLogs('item_finish.log', date("Y-m-d H:i") . "文章已处理完成");
                $is_have = false;
            }
            $data = $article_ids = $item_ids = $article_no_normal_ids = array();
            foreach ($db_data as $article) {
                $content = $this->_get($article['url']);
                $reg_exp = '%<textarea id="gallery-data-textarea" .*?>(.*?)</textarea>%si';
                preg_match($reg_exp, $content, $match);
                $match_data = json_decode($match[1], true);
                if (count($match_data) > 0) {
                    $article_ids[] = $article['id'];
                    foreach ($match_data as $k => $item) {
                        if (isset($item['price']) && (strpos($item['real_url'], 'haohuo.snssdk.com') || strpos($item['real_url'], 'haohuo.jinritemai.com'))) {
                            $shop_goods_id = str_replace('-', '', $item['shop_goods_id']);
                            list($img, $_) = explode('?', $item['img']);
                            $data[$item['id']] = array(
                                'top_line_article_id'         => $article['article_id'],
                                'create_user_id'              => $article['create_user_id'],
                                'top_line_article_title'      => $article['title'],
                                'top_line_article_behot_time' => $article['behot_time'],
                                'shop_goods_id'               => $shop_goods_id,
                                'media_id'                    => $article['media_id'],
                                'name'                        => $item['name'],
                                'price'                       => $item['price'],
                                'price_tag_position'          => $item['price_tag_position'],
                                'description'                 => str_replace(array("/r", "/n", "/r/n"), "", trim($item['description'])),
                                'img'                         => $img,
                                'img_vice'                    => '',
                                'description_vice'            => '',
                            );
                        }
                        if (isset($data[$item['commodity']['id']]) && !$data[$item['commodity']['id']]['img_vice']) {
                            list($img_vice, $_) = explode('?', $item['location']);
                            $data[$item['commodity']['id']]['img_vice']         = $img_vice;
                            $data[$item['commodity']['id']]['description_vice'] = str_replace(array("/r", "/n", "/r/n"), "", trim($item['description']));
                        }
                    }
                } else {
                    $article_no_normal_ids[] = $article['id'];
                }
            }
            if (count($data) > 0) {
                $model = M();
                $model->startTrans();
                try {
                    foreach ($data as $key => $val) {
                        if ($data['img_vice'] || $data['description_vice']) {
                            unset($data[$key]);
                        }
                    }
                    M('temai_items')->addAll(array_values($data));
                    if ($article_ids) {
                        M('temai_article')->where(array('id' => array('in', $article_ids)))->save(array('is_add_item' => 1));
                    }
                    if ($article_no_normal_ids) {
                        M('temai_article')->where(array('id' => array('in', $article_no_normal_ids)))->save(array('is_normal' => 0));
                    }
                    $model->commit();
                    $this->_addLogs('item_success.log', date("Y-m-d H:i") . "添加至商品库成功");
                    sleep(3);
                    continue;

                } catch (\Exception $e) {
                    $model->rollback();
                    $this->_addLogs('item_error.log', date("Y-m-d H:i") . $e->getMessage());
                    sleep(3);
                    continue;
                }
            } else {
                $is_have = false;
                $this->_addLogs('item_error.log', date("Y-m-d H:i") . '组合商品数据失败');
            }
        }
    }

    /**
     * 抓取特卖商品
     */
    public function getProduct() {
        $add    = true;
        $page   = 0;
        $cookie = M('sale_account')->getFieldById(1, 'cookie');
        while ($add === true) {
            $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
            $param   = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'page' => $page);
            $res     = $this->_get($get_url, $param, $cookie);
            if ($res === false) {
                $this->_addLogs('product.log', date("Y-m-d H:i") . '您的登录状态已失效，请联系管理员!');
                $add = false;
            } else {
                $res = json_decode($res, true);
                if ($res['errno'] === 0) {
                    $item_data = $res['goods_infos'];
                    if (count($item_data) == 0) {
                        $this->_addLogs('product.log', date("Y-m-d H:i") . '数据已加载完成!');
                        $add = false;
                    } else {
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
                        if ($page == 0) {
                            M()->execute('truncate ytt_product');
                        }
                        $have_data = M('product')->field('id,platform_sku_id')->where(array('platform_sku_id' => array('in', array_keys($data))))->select();
                        try {
                            foreach ($have_data as $val) {
                                M('product')->where(array('id' => $val['id']))->save($data[$val['platform_sku_id']]);
                                unset($data[$val['platform_sku_id']]);
                            }
                            if ($data) {
                                M('product')->addAll(array_values($data));
                            }
                            $model->commit();
                            $this->_addLogs('product.log', date("Y-m-d H:i") . "第{$page}页数据添加成功");
                            $page++;
                            sleep(2);
                        } catch (\Exception $e) {
                            $model->rollback();
                            $this->_addLogs('product.log', date("Y-m-d H:i") . $e->getMessage());
                            $add = false;
                        }
                    }
                } else {
                    $this->_addLogs('product.log', date("Y-m-d H:i") . $res['msg']);
                    $add = false;
                }
            }
        }

    }

    /**
     * 删除奖励商品库
     */
    public function delReward() {
        $shop_goods_id     = M('reward_product')->getField('shop_goods_id', true);
        $pro_shop_goods_id = M('product')->where(array('platform_sku_id' => array('in', $shop_goods_id)))->getField('platform_sku_id', true);
        $no_shop_goods_id  = array_diff($shop_goods_id, $pro_shop_goods_id);
        if (count($no_shop_goods_id) > 0) {
            M('reward_product')->where(array('shop_goods_id' => array('in', array_values($no_shop_goods_id))))->save(array('status' => 0));
            $no_shop_goods_id['time'] = date('Y-m-d H:i');
            $no_shop_goods_id['msg']  = '删除成功';
            $this->_addLogs('reward.log', $no_shop_goods_id);
        } else {
            $this->_addLogs('reward.log', date("Y-m-d H:i") . '删除失败');
        }
    }

    /**
     * 添加放心购【海淘】商品
     */
    public function fxgItem() {
        $add         = true;
        $page        = 0;
        $cookie      = M('sale_account')->getFieldById(1, 'cookie');
        $item_id_arr = array();
        while ($add === true) {
            $get_url = "http://kolplatform.jinritemai.com/index/article/ajaxGetCommodityList";
            $param   = array('platform' => 3, 'flag' => 0, 'task_pool_flag' => true, 'keyword_type_shop' => 'word', 'keyword_value_shop' => '三多数码专营店', 'page' => $page);
            $res     = $this->_get($get_url, $param, $cookie);
            if ($res === false) {
                $this->_addLogs('fxg_item.log', date("Y-m-d H:i") . '您的登录状态已失效，请联系管理员!');
                $add = false;
            } else {
                $res = json_decode($res, true);
                if ($res['errno'] === 0) {
                    $item_data = $res['goods_infos'];
                    if (count($item_data) == 0) {
                        if (count($item_id_arr) > 0) {
                            M('item', 'ht_', 'FXG_DB')->where(array('item_id' => array('not in', $item_id_arr)))->save(array('status' => 0));
                        }
                        $this->_addLogs('product.log', date("Y-m-d H:i") . '数据已加载完成!');
                        $add = false;
                    } else {
                        $data = array();
                        foreach ($item_data as $item) {
                            $item_id_arr[]                  = $item['platform_sku_id'];
                            $data[$item['platform_sku_id']] = array(
                                'item_id'        => $item['platform_sku_id'],
                                'title'          => $item['sku_title'],
                                'price'          => $item['sku_price'],
                                'pic'            => $item['figure'],
                                'hotrank'        => $item['hotrank'],
                                'month_sell_num' => $item['month_sell_num'],
                                'cos_fee'        => $item['cos_info']['cos_fee'],
                                'cos_ratio'      => $item['cos_info']['cos_ratio'],
                            );
                        }
                        $item_id_data = array_keys($data);
                        $have_item    = M('item', 'ht_', 'FXG_DB')->where(array('item_id' => array('in', $item_id_data)))->getField('item_id', true);
                        foreach ($have_item as $item_id) {
                            M('item', 'ht_', 'FXG_DB')->where(array('item_id' => $item_id))->save($data[$item_id]);
                            unset($data[$item_id]);
                        }
                        if (count($data) > 0) {
                            M('item', 'ht_', 'FXG_DB')->addAll(array_values($data));
                            $this->_addLogs('fxg_item.log', $data);
                        } else {
                            $this->_addLogs('fxg_item.log', '商品库没有新商品需要添加');
                        }
                        $page++;
                        sleep(2);
                    }
                } else {
                    $this->_addLogs('fxg_item.log', date("Y-m-d H:i") . $res['msg']);
                    $add = false;
                }
            }
        }
    }

}