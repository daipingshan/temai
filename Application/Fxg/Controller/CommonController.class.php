<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:19
 */

namespace Fxg\Controller;

use Common\Controller\CommonBaseController;

/**
 * Class CommonController
 *
 * @package Admin\Controller
 */
class CommonController extends CommonBaseController {

    /**
     * @var string
     */
    protected $open_url = "http://openapi.jinritemai.com/";

    /**
     * @var bool
     */
    protected $checkUser = true;

    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * @var array
     */
    protected $user_info = array();

    /**
     * CommonController constructor.
     */
    public function __construct() {
        parent::__construct();
        if ($this->checkUser == true) {
            $this->_checkUser();
        }
        $this->assign('controller_name', CONTROLLER_NAME);
    }

    /**
     * 检测用户登录状态
     */
    protected function _checkUser() {
        $user_info = session('fxg_user_info');
        if ($user_info) {
            $this->user_id   = session('fxg_user_id');
            $this->user_info = $user_info;
        } else {
            if (IS_AJAX) {
                $this->error('请登录');
            } else {
                $this->redirect('Login/index');
            }
        }
    }


    /**
     * 表格输出数据格式
     *
     * @param array $data
     * @param int $count
     * @param int $code
     * @param string $msg
     */
    protected function output($data = array(), $count = 0, $code = 0, $msg = 'ok') {
        die(json_encode(array('code' => $code, 'count' => $count, 'data' => $data, 'msg' => $msg)));
    }

    /**
     * @param $shop_id
     * @param $param
     * @param $method
     * @return array
     */
    protected function _getOpenData($shop_id, $param, $method) {
        $shop_info  = $this->_getShopCache($shop_id);
        $date       = date('Y-m-d H:i:s');
        $param_json = $this->_createParam($param);
        $sign       = $this->_createSign($shop_info, $param_json, $method, $date);
        $param      = array(
            'app_key'    => $shop_info['app_key'],
            'method'     => $method,
            'param_json' => $param_json,
            'timestamp'  => $date,
            'v'          => 1,
            'sign'       => $sign,
        );
        $url        = $this->open_url . str_replace('.', '/', $method);
        $res        = json_decode($this->_get($url, $param), true);
        if ($res['err_no'] === 0) {
            return array('status' => 1, 'data' => $res['data'], 'info' => 'success');
        } else {
            return array('status' => 0, 'info' => $res['message']);
        }
    }

    /**
     * @param $shop_info
     * @param $param_json
     * @param $method
     * @param $date
     * @return string
     */
    protected function _createSign($shop_info, $param_json, $method, $date) {
        $str  = "{$shop_info['app_secret']}app_key{$shop_info['app_key']}method{$method}param_json{$param_json}timestamp{$date}v1{$shop_info['app_secret']}";
        $sign = md5($str);
        return $sign;
    }

    /**
     * @param $param
     * @return string
     */
    protected function _createParam($param) {
        $param = $this->_toString($param);
        ksort($param);
        $param_json = json_encode($param);
        return $param_json;
    }

    /**
     * @param $shop_id
     * @return mixed
     */
    protected function _getShopCache($shop_id) {
        $data = S('shop');
        if (empty($data)) {
            $data = M('shop')->index('id')->select();
            S('shop', $data);
        }
        return $data[$shop_id] ? : '';
    }

    /**
     * @return mixed
     */
    protected function _getShop() {
        $data = S('shop');
        if (empty($data)) {
            $data = M('shop')->index('id')->select();
            S('shop', $data);
        }
        return $data;
    }


    /**
     * @param $logistics_id
     * @return mixed
     */
    protected function _getLogistics($logistics_id) {
        $data = S('logistics');
        if (empty($data)) {
            $data = M('logistics')->index('id')->select();
            S('logistics', $data);
        }
        return $data[$logistics_id] ? : '';
    }


    /**
     * @return mixed
     */
    protected function _getLogisticsList() {
        $data = S('logistics_list');
        if (empty($data)) {
            $data = M('logistics_list')->index('english_name')->select();
            S('logistics_list', $data);
        }
        return $data;
    }

    /**
     * @return mixed
     */
    protected function _getLogisticsCache() {
        $data = S('logistics_cache');
        if (empty($data)) {
            $data = M('logistics_list')->index('logistics_id')->select();
            S('logistics_cache', $data);
        }
        return $data;
    }


    /**
     * @param $param
     * @return mixed
     */
    protected function _toString($param) {
        if (!empty($param)) {
            foreach ($param as &$val) {
                $val = strval($val);
            }
        }
        return $param;
    }


}