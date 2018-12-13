<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 14:45
 */

namespace Admin\Controller;

/**
 * Class IndexController
 *
 * @package Admin\Controller
 */
class IndexController extends CommonController {


    /**
     * 错误排查
     */
    public function index() {
        $model = M('illegal_article');
        $count = $model->count();
        $page  = $this->pages($count, 50);
        $limit = $page->firstRow . ',' . $page->listRows;
        $data  = $model->limit($limit)->order('id desc')->select();
        $data  = array(
            'page' => $page->show(),
            'data' => $data,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 上传文件
     */
    public function addIllegalArticle() {
        $filename = $_FILES['filename'];
        if (!$filename) {
            $this->error('未发现文件！');
        }
        list($name, $ext) = explode('.', $filename['name']);
        $file_path = ROOT_PATH . "/Uploads/" . date("Y-m-d") . $ext;
        @move_uploaded_file($filename['tmp_name'], $file_path);
        if (file_exists($file_path)) {
            require_once(APP_PATH . "/Common/Org/PHPExcel.class.php");
            require_once(APP_PATH . "/Common/Org/PHPExcel/IOFactory.php");
            $reader   = \PHPExcel_IOFactory::createReader('Excel2007');
            $PHPExcel = $reader->load($file_path); // 载入excel文件
            $obj      = $PHPExcel->getSheet(0);// 读取第一個工作表
            $data     = $obj->toArray();
            unset($data[0]);
            $return_data = array();
            foreach ($data as $val) {
                $title         = mb_convert_encoding($val[0], "UTF-8", "auto");
                $reason        = mb_convert_encoding($val[1], "UTF-8", "auto");
                $return_data[] = array('title' => $title, 'reason' => $reason);
            }
            $add_data = array_reverse($return_data);
            if ($add_data) {
                M('illegal_article')->addAll($return_data);
            }
            $this->success('上传成功');
        } else {
            $this->error('上传失败！');
        }

    }
}