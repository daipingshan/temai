<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/4
 * Time: 10:33
 */

namespace Admin\Controller;

/**
 * 订单管理
 * Class OrderController
 *
 * @package Admin\Controller
 */
class OrderController extends CommonController {


    /**
     * 订单列表
     */
    public function index() {
        $type = I('get.type', 'day', 'trim');
        if ($type == 'day') {
            $start_time = strtotime(date('Y-m-d'));
            $end_time   = $start_time + 86399;
        } else {
            $start_time = strtotime(date('Y-m-d', strtotime('-1 days')));
            $end_time   = $start_time + 86399;
        }
        $where      = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单付款', '订单完成', '订单结算')));
        $data       = M('fxg')->where($where)->order('pay_time desc')->select();
        $user_count = array();
        $money      = $day_user_money = 0;
        foreach ($data as $val) {
            $user_count[$val['name']] = array('money' => $user_count[$val['name']]['money'] + $val['income'], 'num' => $user_count[$val['name']]['num'] + 1);
            $money                    += $val['income'];
        }
        if (isset($user_count[$this->user_info['name']])) {
            $day_user_money = $user_count[$this->user_info['name']]['money'];
        }
        arsort($user_count);
        $start_time = strtotime(date('Y-m'));
        $end_time   = time();
        $where      = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单付款', '订单完成', '结算完成')));
        $user_data  = M('fxg')->field('sum(income) as money,name')->where($where)->group('name')->order('money desc')->select();
        $this->assign(array('data' => $data, 'user_count' => $user_count, 'type' => $type, 'money' => $money, 'user_data' => $user_data, 'day_user_money' => $day_user_money));
        $this->display();
    }

    /**
     * 认领订单
     */
    public function claim() {
        $where = "`name` IS NULL or `name`='待认领'";
        $data  = M('fxg')->where($where)->order('pay_time desc')->select();
        foreach ($data as &$val) {
            $val['author_name'] = $this->author[$val['author_id']];
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 认领并保存订单
     */
    public function doClaim() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('fxg')->where(array('name' => '待认领', 'id' => $id))->find();
        if (!$id || empty($info)) {
            $this->error('请求参数不合法！');
        }
        $save_data = array('name' => $this->user_info['name'], 'user_id' => $this->user_id);
        $res       = M('fxg')->where(array('article_title' => $info['article_title'], 'name' => '待认领'))->save($save_data);
        if ($res) {
            $this->success('认领成功');
        } else {
            $this->error('认领失败！');
        }
    }

    /**
     * 业绩排行
     */
    public function achievement() {
        $where                    = array('order_status' => array('in', array('订单付款', '订单完成', '结算完成')));
        $complete_where           = array('order_status' => array('in', array('订单完成', '结算完成')));
        $settle_where             = array('order_status' => '结算完成');
        $start_time               = strtotime(date('Y-m'));
        $end_time                 = time();
        $month_where              = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)));
        $month_complete_where     = array('UNIX_TIMESTAMP(complete_time)' => array('between', array($start_time, $end_time)));
        $month_settle_where       = array('UNIX_TIMESTAMP(settle_time)' => array('between', array($start_time, $end_time)));
        $start_time               = strtotime(date('Y-m', strtotime('-1 months')));
        $end_time                 = strtotime(date('Y-m')) - 1;
        $pro_month_where          = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)));
        $pro_month_complete_where = array('UNIX_TIMESTAMP(complete_time)' => array('between', array($start_time, $end_time)));
        $pro_month_settle_where   = array('UNIX_TIMESTAMP(settle_time)' => array('between', array($start_time, $end_time)));
        $month_data               = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($month_where)->where($where)->order('money desc')->group('name')->select();
        $month_complete_data      = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($month_complete_where)->where($complete_where)->order('money desc')->group('name')->select();
        $month_settle_data        = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($month_settle_where)->where($settle_where)->order('money desc')->group('name')->select();
        $pro_month_data           = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($pro_month_where)->where($where)->order('money desc')->group('name')->select();
        $pro_month_complete_data  = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($pro_month_complete_where)->where($complete_where)->order('money desc')->group('name')->select();
        $pro_month_settle_data    = M('fxg')->alias('r')->field('name,sum(income) as money,count(id) as num')->where($pro_month_settle_where)->where($settle_where)->order('money desc')->group('name')->select();
        $month_money              = $month_complete_money = $month_settle_money = $pro_month_money = $pro_month_complete_money = $pro_month_settle_money = 0;
        foreach ($month_data as $val) {
            $month_money += $val['money'];
        }
        foreach ($month_complete_data as $val) {
            $month_complete_money += $val['money'];
        }
        foreach ($month_settle_data as $val) {
            $month_settle_money += $val['money'];
        }
        foreach ($pro_month_data as $pro_val) {
            $pro_month_money += $pro_val['money'];
        }
        foreach ($pro_month_complete_data as $pro_val) {
            $pro_month_complete_money += $pro_val['money'];
        }
        foreach ($pro_month_settle_data as $pro_val) {
            $pro_month_settle_money += $pro_val['money'];
        }
        $assign = array(
            'month_data'               => $month_data,
            'month_complete_data'      => $month_complete_data,
            'month_settle_data'        => $month_settle_data,
            'pro_month_data'           => $pro_month_data,
            'pro_month_complete_data'  => $pro_month_complete_data,
            'pro_month_settle_data'    => $pro_month_settle_data,
            'month_money'              => $month_money,
            'month_complete_money'     => $month_complete_money,
            'month_settle_money'       => $month_settle_money,
            'pro_month_money'          => $pro_month_money,
            'pro_month_complete_money' => $pro_month_complete_money,
            'pro_month_settle_money'   => $pro_month_settle_money,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 奖励排行
     */
    public function rewardRank() {
        $where                  = array('o.order_status' => array('in', array('订单完成', '订单付款', '结算完成')));
        $settle_where           = array('o.order_status' => array('in', array('订单完成', '结算完成')));
        $start_time             = strtotime(date('Y-m'));
        $end_time               = time();
        $month_where            = array('UNIX_TIMESTAMP(o.pay_time)' => array('between', array($start_time, $end_time)));
        $month_settle_where     = array('UNIX_TIMESTAMP(o.settle_time)' => array('between', array($start_time, $end_time)));
        $start_time             = strtotime(date('Y-m', strtotime('-1 months')));
        $end_time               = strtotime(date('Y-m')) - 1;
        $pro_month_where        = array('UNIX_TIMESTAMP(o.pay_time)' => array('between', array($start_time, $end_time)));
        $pro_month_settle_where = array('UNIX_TIMESTAMP(o.settle_time)' => array('between', array($start_time, $end_time)));
        $month_data             = M('reward_product')->alias('r')->field('name,sum(reward_money) as money')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->where($month_where)->order('money desc')->group('name')->select();
        $month_settle_data      = M('reward_product')->alias('r')->field('name,sum(reward_money) as money')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($settle_where)->where($month_settle_where)->order('money desc')->group('name')->select();
        $pro_month_data         = M('reward_product')->alias('r')->field('name,sum(reward_money) as money')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->where($pro_month_where)->order('money desc')->group('name')->select();
        $pro_month_settle_data  = M('reward_product')->alias('r')->field('name,sum(reward_money) as money')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($settle_where)->where($pro_month_settle_where)->order('money desc')->group('name')->select();
        $month_money            = $month_settle_money = $pro_month_money = $pro_month_settle_money = 0;
        foreach ($month_data as $val) {
            $month_money += $val['money'];
        }
        foreach ($month_settle_data as $val) {
            $month_settle_money += $val['money'];
        }
        foreach ($pro_month_data as $pro_val) {
            $pro_month_money += $pro_val['money'];
        }
        foreach ($pro_month_settle_data as $pro_val) {
            $pro_month_settle_money += $pro_val['money'];
        }
        $assign = array(
            'month_data'             => $month_data,
            'month_settle_data'      => $month_settle_data,
            'pro_month_data'         => $pro_month_data,
            'pro_month_settle_data'  => $pro_month_settle_data,
            'month_money'            => $month_money,
            'month_settle_money'     => $month_settle_money,
            'pro_month_money'        => $pro_month_money,
            'pro_month_settle_money' => $pro_month_settle_money,
        );
        $this->assign($assign);
        $this->display();
    }


    /**
     * 订单统计
     */
    public function count() {
        $time    = I('get.time', 1, 'int');
        $times   = I('get.times', '', 'trim');
        $user_id = I('get.user_id', 0, 'int');
        $title   = I('get.title', '', 'trim');
        if ($this->group_id != 1) {
            $user_id = $this->user_id;
        }
        $user = M('tmuser')->where(array('group_id' => array('neq', 1)))->getField('id,name');
        if ($time == 2) {
            $end_time   = strtotime(date('Y-m-d')) - 1;
            $start_time = strtotime(date('Y-m-d', strtotime('-1 days')));
        } else if ($time == 3) {
            $end_time   = strtotime(date('Y-m-d', strtotime('-1 days'))) - 1;
            $start_time = strtotime(date('Y-m-d', strtotime('-2 days')));
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-d'));
        }
        if ($times) {
            list($start_time, $end_time) = explode(' ~ ', $times);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time) + 86399;
            }
        }
        $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '订单付款')));
        if ($user_id) {
            $where['name'] = $user[$user_id];
        }
        if ($title) {
            $where['article_title'] = array('like', "%{$title}%");
        }
        $field = 'left(pay_time,10) as time ,count(id) as news_num,name,sum(income) as income,order_source,article_title,id';
        $data  = M('fxg')->field($field)->where($where)->group('time,article_title')->order('time desc,income desc,news_num desc')->select();
        $this->assign('user', $user);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 文章订单统计
     */
    public function articleOrder() {
        $user_id = I('get.user_id', 0, 'int');
        $time    = I('get.time', '', 'trim,urldecode');
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time);
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-01'));
            $time       = date('Y-m-d', $start_time) . ' ~ ' . date('Y-m-d', $end_time);
        }
        $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '订单付款')));
        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        $field = 'count(id) as news_num,name,sum(income) as income,order_source,article_title,left(pay_time,10) as time,id';
        $data  = M('fxg')->field($field)->where($where)->group('article_title')->order('income desc,news_num desc')->select();
        $this->assign('data', $data);
        $this->assign('time', $time);
        $this->display();
    }

    /**
     * 订单采集
     */
    public function orderCollection() {
        $user_id_data = array(2, 3, 4, 7, 8, 9, 10, 14, 5,16,23,24);
        $user_data    = M('sale_account')->where(array('id' => array('in', $user_id_data)))->getField('username,cookie');
        $this->assign('user_data', $user_data);
        $this->display();
    }

    /**
     * 订单采集
     */
    public function ajaxOrderCollection() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $cookie    = I('post.cookie', '', 'trim');
        $user_data = I('post.user_name', '', 'trim');
        $type      = I('post.type', 1, 'int');
        $time      = I('post.time', '', 'trim');
        $page      = I('post.page', 1, 'int');
        if (empty($cookie)) {
            $this->error('请选择特卖账号！');
        }
        if (empty($time)) {
            $this->error('请选择时间范围！');
        }
        list($start_time, $end_time) = explode(' ~ ', $time);
        if (empty($start_time) || empty($end_time)) {
            $this->error('时间格式不符合要求');
        }
        if (strtotime($end_time) - strtotime($start_time) >= 15 * 86400) {
            $this->error('时间范围不能超过15天！');
        }
        if ($type == 1) {
            $url = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time={$start_time}&end_time={$end_time}&order_status=&time_type=0&pageNumber={$page}&pageSize=80";
        } else if ($type == 2) {
            $url = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time={$start_time}&end_time={$end_time}&order_status=&time_type=1&pageNumber={$page}&pageSize=80";
        } else {
            $url = "http://kolplatform.jinritemai.com/index/benefit/ajaxGetOrderDetail?start_time={$start_time}&end_time={$end_time}&order_status=&time_type=2&pageNumber={$page}&pageSize=80";
        }
        $res = $this->_get($url, array(), $cookie);
        if ($res === false) {
            $this->error('该账号cookie已过期，请更新后再采集！');
        }
        $result = json_decode($res, true);
        if (empty($result)) {
            $this->error('获取数据失败，请重新操作！');
        }
        $data = $add_data = array();
        foreach ($result as $key => $val) {
            if (is_numeric($key)) {
                unset($val['id']);
                $val['article_title']   = trim($val['article_title']);
                $val['order_status']    = trim($val['order_status']);
                $goods_id               = get_word($val['commodity_info'], 'id=', '&');
                $val['goods_id']        = $goods_id;
                $data[$val['order_id']] = $val;
            }
        }
        if (empty($data)) {
            $this->error('success');
        }
        if ($user_data == 'D账号') {
            $fxg      = M('gg_fxg');
            $usernews = M('gg_tmnews');
            $gdzhi    = '52时尚';
        } else {
            $fxg      = M('fxg');
            $usernews = M('tmnews');
            $gdzhi    = '待认领';
        }

        $order_id_key = array_keys($data);
        $order_data   = $fxg->where(array('order_id' => array('in', $order_id_key)))->getField('order_id,order_status');

        $model = M();
        $model->startTrans();
        try {
            foreach ($data as $v) {
                if (isset($order_data[$v['order_id']])) {
                    $fxg->where(array('order_id' => $v['order_id']))->save($v);
                } else {
                    $news_info = $usernews->where(array('title' => $v['article_title']))->find();
                    if ($news_info) {
                        $v['name']    = $news_info['name'];
                        $v['news_id'] = $news_info['id'];
                        $v['user_id'] = $news_info['user_id'];
                    } else {
                        $v['name']    = $gdzhi;
                        $v['news_id'] = 0;
                        $v['user_id'] = 0;
                    }
                    $add_data[] = $v;
                }
            }
            //var_dump($add_data);exit;
            $add_count    = count($add_data);
            $update_count = count($data) - $add_count;
            if ($add_count > 0) {
                $fxg->addAll($add_data);
            }
            $model->commit();
            sleep(1);
            $this->success("第{$page}页订单抓取成功，更新{$update_count}条，添加{$add_count}条!");
        } catch (\Exception $e) {
            $model->rollback();
            $this->error($e->getMessage());
        }
    }

    /**
     * 我的订单
     */
    public function myOrder() {
        $start_time = strtotime(date('Y-m') . '-01');
        $end_time   = time();
        $where      = array('name' => $this->user_info['name'], 'order_status' => array('in', array('订单完成', '订单付款', '结算完成')), 'UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)));
        $money      = M('fxg')->where($where)->sum('income');
        unset($where['order_status']);
        $count     = M('fxg')->where($where)->count();
        $page      = $this->pages($count, $this->limit);
        $limit     = $page->firstRow . ',' . $page->listRows;
        $data      = M('fxg')->where($where)->limit($limit)->order('id desc')->select();
        $user      = M('tmuser')->where(array('zzname' => $this->user_info['name']))->getField('name', true);
        $user_data = array();
        if (!empty($user) && count($user) > 1) {
            unset($where['name']);
            $where['order_status'] = array('in', array('订单完成', '订单付款', '结算完成'));
            $user_data             = M('fxg')->field('sum(income) as money,name')->where($where)->group('name')->order('money desc')->select();
        }

        $assign = array(
            'page'      => $page->show(),
            'data'      => $data,
            'money'     => $money ? : 0,
            'user_data' => $user_data,
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 我的奖励
     */
    public function myReward() {
        $type = I('get.type', 'month', 'trim');
        if ($type == 'month') {
            $start_time = strtotime(date('Y-m'));
            $end_time   = time();
        } else {
            $start_time = strtotime(date('Y-m', strtotime('-1 month')));
            $end_time   = strtotime(date('Y-m')) - 1;
        }
        $where                           = array('o.name' => $this->user_info['name'], 'o.order_status' => array('in', array('订单完成', '订单付款', '结算完成')), 'UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)));
        $money                           = M('reward_product')->alias('r')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->sum('reward_money');
        $settle_where                    = array('o.name' => $this->user_info['name'], 'o.order_status' => '结算完成', 'UNIX_TIMESTAMP(settle_time)' => array('between', array($start_time, $end_time)));
        $settle_money                    = M('reward_product')->alias('r')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($settle_where)->sum('reward_money');
        $where                           = array();
        $map['UNIX_TIMESTAMP(pay_time)'] = array('between', array($start_time, $end_time));
        $map['_logic']                   = 'or';
        $where['_complex']               = $map;
        $where['o.name']                 = $this->user_info['name'];
        $where['o.order_status']         = array('in', array('订单完成', '订单付款', '结算完成'));
        if ($this->group_id == 1) {
            unset($where['o.name'], $settle_where['o.name']);
            $money_data        = M('reward_product')->alias('r')->field('sum(reward_money) as reward_money,name')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->group('name')->order('reward_money desc')->select();
            $settle_money_data = M('reward_product')->alias('r')->field('sum(reward_money) as reward_money,name')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($settle_where)->group('name')->order('reward_money desc')->select();
            $this->assign(array('money_data' => $money_data, 'settle_money_data' => $settle_money_data));
            $data = M('reward_product')->alias('r')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->select();
        } else {
            $data = M('reward_product')->alias('r')->join('left join ytt_fxg o ON o.goods_id = r.shop_goods_id')->where($where)->select();
        }
        $this->assign(array('type' => $type, 'money' => $money ? : 0, 'settle_money' => $settle_money ? : 0, 'data' => $data));
        $this->display();
    }

    /**
     * 失效订单
     */
    public function invalidOrder() {
        $type  = I('get.type', 'month', 'trim');
        $where = array('order_status' => '订单失效');
        if ($type == 'month') {
            $start_time = strtotime(date('Y-m'));
            $end_time   = strtotime(date('Y-m-d')) + 86399;
        } else {
            $start_time = strtotime(date('Y-m', strtotime('-1 months')));
            $end_time   = strtotime(date('Y-m')) - 1;
        }
        if ($this->group_id != 1) {
            $where['name'] = $this->user_info['name'];
        }

        $where['UNIX_TIMESTAMP(pay_time)'] = array('between', array($start_time, $end_time));

        $count      = M('fxg')->where($where)->count();
        $page       = $this->pages($count, $this->limit);
        $limit      = $page->firstRow . ',' . $page->listRows;
        $data       = M('fxg')->where($where)->limit($limit)->order('pay_time desc')->select();
        $user_count = M('fxg')->where($where)->field('sum(income) as money,name')->order('money desc')->group('name')->select();
        $this->assign(array('data' => $data, 'page' => $page->show(), 'user_count' => $user_count, 'type' => $type));
        $this->display();
    }

    /**
     * 订单分析
     */
    public function orderCount() {
        $time    = I('get.time', '', 'trim');
        $user_id = I('get.user_id', '', 'trim');
        $btn     = I('get.btn', '', 'trim');
        if ($time) {
            list($start_time, $end_time) = explode(' ~ ', $time);
            if ($start_time && $end_time) {
                $start_time = strtotime($start_time);
                $end_time   = strtotime($end_time) + 86399;
            }
        } else {
            $end_time   = time();
            $start_time = strtotime(date('Y-m-d'));
        }
        $where = array('UNIX_TIMESTAMP(pay_time)' => array('between', array($start_time, $end_time)), 'order_status' => array('in', array('订单完成', '结算完成', '订单付款')));
        if ($user_id) {
            $where['user_id'] = array('in', $user_id);
        }
        $data       = M('fxg')->where($where)->field('*,count(id) as num,sum(income) as commission')->group('goods_id,user_id')->order('num desc')->select();
        $goods_data = M('shop_product')->getField('platform_sku_id,shop_name');
        foreach ($data as &$val) {
            if (isset($goods_data[$val['goods_id']])) {
                $val['shop_name'] = $goods_data[$val['goods_id']];
            } else {
                $val['shop_name'] = '未知';
            }
        }
        if ($btn == 'down') {
            $key_name  = array(
                'commodity_name' => '商品标题',
                'name'           => '用户名称',
                'num'            => '出单数量',
                'commission'     => '总佣金',
                'shop_name'      => '店铺名称',
            );
            $file_name = '订单分析';
            if ($time) {
                $file_name = "订单分析{$time}";
            }
            download_xls($data, $key_name, $file_name);
        } else {
            $user_data = M('tmuser')->getField('id,name');
            $this->assign(array('user_data' => $user_data, 'data' => $data, 'user_ids' => $user_id));
            $this->display();
        }

    }
}
