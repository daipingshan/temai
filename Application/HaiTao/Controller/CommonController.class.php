<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:19
 */

namespace HaiTao\Controller;

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
        $this->assign('controller_name', CONTROLLER_NAME);
    }

    /**
     * 检测用户登录状态
     */
    protected function _checkUser() {
        $user_info = session('ht_user_info');
        if ($user_info) {
            $this->user_id   = session('ht_user_id');
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


}