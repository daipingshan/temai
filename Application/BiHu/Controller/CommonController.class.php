<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:19
 */

namespace BiHu\Controller;

use Common\Controller\CommonBaseController;

/**
 * Class CommonController
 *
 * @package Admin\Controller
 */
class CommonController extends CommonBaseController {

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
        $auth_list = C('AUTH_LIST');
        $this->assign('menu_list', $auth_list);
    }

    /**
     * 检测用户登录状态
     */
    protected function _checkUser() {
        $user_info = session('admin_user_info');
        if ($user_info) {
            $this->user_id   = session('admin_user_id');
            $this->user_info = $user_info;
        } else {
            if (IS_AJAX) {
                $this->error('请登录');
            } else {
                $this->redirect('Login/index');
            }
        }
    }


}