<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:19
 */

namespace Admin\Controller;

use Common\Controller\CommonBaseController;
use Common\Org\TranslateSDK;

/**
 * Class CommonController
 *
 * @package Admin\Controller
 */
class CommonController extends CommonBaseController {

    /**
     * @var string
     */
    protected $source_id = '8236544';

    /**
     * @var string
     */
    protected $top_line_source_id = '16568122';

    /**
     * @var bool
     */
    protected $checkUser = true;

    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * @var int
     */
    protected $group_id = 0;

    /**
     * @var array
     */
    protected $user_info = array();

    /**
     * @var string
     */
    protected $pid = 'mm_29479672_14502011_57114279';


    /**
     * @var array
     */
    protected $author = array(
        '1981' => 'dr_xiaoyi',
        '2970' => 'dr_superechao',
        '8543' => 'ffwl5865@163.com',
        '7755' => 'ewskivxf9398369@163.com',
        '7845' => 'rsygc9212014@163.com',
        '3811' => 'dr_atu1988',
        '2411' => 'dr_meizhuanggou',
        '3230' => 'dr_119394050',
        '944'  => ' dr_qixingquan',
        '1528' => 'dr_chuanyidaban',
        '1675' => 'dr_52fashion',
    );

    /**
     * CommonController constructor.
     */
    public function __construct() {
        parent::__construct();
        if ($this->checkUser == true) {
            $this->_checkUser();
        }
        $auth_list = C('AUTH_LIST');
        $this->assign('auth_list', $auth_list);
        $this->assign('menu_list', $auth_list[$this->group_id]['data']);
        $this->assign('group_id', $this->group_id);
        $this->assign('user_id', $this->user_id);
    }

    /**
     * 检测用户登录状态
     */
    protected function _checkUser() {
        $user_info = session('admin_user_info');
        if ($user_info) {
            $this->user_id   = session('admin_user_id');
            $this->user_info = $user_info;
            $this->pid       = $this->user_info['pid'];
            $this->group_id  = $this->user_info['group_id'];
            if ($this->user_info['source_id'] > 0) {
                $this->top_line_source_id = $this->user_info['source_id'];
            }
        } else {
            if (IS_AJAX) {
                $this->error('请登录');
            } else {
                $this->redirect('Login/index');
            }
        }
    }


    /**
     * 获取头条默认账号信息
     */
    protected function _getTopLineCookie() {
        $data = S('top_line_cookie');
        if (!$data) {
            $data = M('top_line_account')->getFieldById(1, 'cookie');
            if ($data) {
                S('top_line_cookie', $data);
            }
        }
        return $data;
    }

    /**
     * 获取头条账号信息
     */
    protected function _getTopLineAccount() {
        $data = S('top_line_account');
        if (!$data) {
            $data = M('top_line_account')->where(array('id' => array('neq', 1)))->index('id')->select();
            if ($data) {
                S('top_line_account', $data);
            }
        }
        return $data;
    }

    /**
     * 获取特卖账号信息
     */
    protected function _getSaleAccount() {
        $data = S('sale_account');
        if (!$data) {
            $data = M('sale_account')->where(array('id' => array('neq', 1)))->index('id')->select();
            if ($data) {
                S('sale_account', $data);
            }
        }
        return $data;
    }

    /**
     * 获取特卖默认账号信息
     */
    protected function _getSaleCookie() {
        $cookie = S('sale_cookie');
        if (!$cookie) {
            $cookie = M('sale_account')->getFieldById(1, 'cookie');
            if ($cookie) {
                S('sale_cookie', $cookie);
            }
        }
        return $cookie;
    }


    /**
     * 获取头条默认账号信息
     */
    protected function _getUserData() {
        $data = S('sale_user_data');
        if (!$data) {
            $data = M('tmuser')->where(array('group_id' => 2))->select();
            if ($data) {
                S('sale_user_data', $data);
            }
        }
        return $data;
    }

    /**
     * 获取头条默认账号信息
     */
    protected function _getUserAllData() {
        $data = S('sale_user_all_data');
        if (!$data) {
            $data = M('tmuser')->select();
            if ($data) {
                S('sale_user_all_data', $data);
            }
        }
        return $data;
    }

    /**
     * 获取缓存商品
     */
    protected function _getItemCache() {
        $user_id = $this->user_id;
        return S('sale_item_' . $user_id) ? : array();
    }

    /**
     * 删除缓存商品
     */
    protected function _delItemCache() {
        $user_id = $this->user_id;
        S('sale_item_' . $user_id, null);
    }


    /**
     * 百度翻译
     */
    public function translate() {
        if (!IS_AJAX) {
            $this->error(array('msg' => '非法请求！'));
        }
        $content = I('post.content', '', 'trim');
        if (!$content) {
            $this->error(array('msg' => '翻译内容不能为空！'));
        }
        $obj = new TranslateSDK();
        $res = $obj->translate($content, 'zh', 'en');
        $res = $obj->translate($res['trans_result'][0]['dst'], 'en', 'zh');
        $this->success(array('msg' => '翻译成功', 'content' => $res['trans_result'][0]['dst']));
    }

    /**
     * @param $where
     * @param $filter
     */
    protected function _getLastId(&$where, &$filter) {
        $last_id = S('temai_item_last_id') ? : 0;
        if ($this->group_id > 1 && $last_id > 0) {
            $where['id'] = array('lt', $last_id);
            if ($filter) {
                $filter .= ' AND id<' . $last_id;
            } else {
                $filter = 'id<' . $last_id;
            }
        }
    }

      /**
     * 检测文章标题
     */
    public function checkTitle() {
        $title = I('get.title', '', 'trim,urldecode');
        if (empty($title)) {
            $this->error('文章标题不能为空!');
        }
        $title = urlencode($title);
        $url   = "https://mp.toutiao.com/check_title/?title={$title}";
        $res   = $this->_get($url, array(), $this->_getTopLineCookie());
        $res   = json_decode($res, true);
        if ($res['message'] == 'success') {
            $this->success($res['check_title_msg'] == 'success' ? '正常文章' : $res['check_title_msg']);
        } else if ($res['message'] == 'error') {
            $this->error('cookie已失效!');
        } else {
            $this->error('返回数据异常！');
        }
    }


}