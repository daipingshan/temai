<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:15
 */

namespace Common\Controller;

use Think\Controller;
use Think\Page;

/**
 * Class CommonController
 *
 * @package Common\Controller
 */
class CommonBaseController extends Controller {

    /**
     * @var bool
     */
    protected $openSearchStatus = true;

    /**
     * @var string
     */
    protected $http_referer = "https://mp.toutiao.com";

    /**
     * @var int
     */
    protected $limit = 20;

    /**
     * @var string
     */
    protected $source_id = "16568506";

    /**
     * 刷阅读量作者
     */    
    protected $create_user_id = array(3227,8363,2882,7783);

    /**
     * CommonBaseController constructor.
     */
    public function __construct() {
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
    }

    /**
     * @param $totalRows
     * @param $listRows
     * @param array $map
     * @param int $rollPage
     * @return Page
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val             = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('header', '条信息');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig(
            'theme', '<div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-1"><span>当前页' . $listRows . '条数据 总%TOTAL_ROW% %HEADER%</span>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%</div>'
        );
        return $Page;
    }

    /**
     * @param $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _get($url, $params = array(), $cookie = '') {
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
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
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
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
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
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _file($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
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
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
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
     * 二维数组排序
     *
     * @param $data
     * @param $sort_order_field
     * @return mixed
     */
    protected function _arraySort($data, $sort_order_field) {
        if (!$data) {
            return array();
        }
        foreach ($data as $val) {
            $key_arrays[] = $val[$sort_order_field];
        }
        array_multisort($key_arrays, SORT_ASC, SORT_NUMERIC, $data);
        return $data;
    }


    /**
     * @param $filename
     * @param $data
     */
    protected function _addLogs($filename, $data) {
        $path = "/logs/" . date('Y-m-d');
        if (!is_dir($path)) {
            @mkdir($path);
        }
        $path = $path . "/" . $filename;
        if (is_array($data)) {
            file_put_contents($path, var_export($data, true) . "\r\n", FILE_APPEND);
        } else {
            file_put_contents($path, $data . "\r\n", FILE_APPEND);
        }
    }

    /**
     * @return mixed
     */
    protected function _getProxy() {
        $proxy_data = array(
            "121.199.6.124:16817",
            "121.42.63.89:16817",
            "114.67.143.11:16817",
            "120.24.216.121:16817",
            "110.76.185.162:16817",
            "114.215.140.117:16817",
            "122.114.82.64:16817",
            "47.92.127.154:16817",
            "116.62.113.134:16817",
            "122.114.214.159:16817"
        );
        $num        = rand(0, 9);
        if (isset($proxy_data[$num])) {
            $ip = $proxy_data[$num];
        } else {
            $ip = $proxy_data[0];
        }
        return $ip;
    }
}