<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 17:04
 */

/**
 * @param $str
 * @return string
 */
function encrypt_pwd($str) {
    return md5(trim($str) . C('PWD_ENCRYPT_STR'));
}

/**
 * @param $html
 * @param $star
 * @param $end
 * @return mixed
 */
function get_word($html, $star, $end) {
    $wd  = '';
    $pat = '/' . $star . '(.*?)' . $end . '/s';
    if (!preg_match_all($pat, $html, $mat)) {
    } else {
        $wd = $mat[1][0];
    }
    return $wd;
}

/**
 * @param $data
 * @param $keynames
 * @param string $name
 */
function download_xls($data, $keynames, $name = 'dataxls') {

    $xls[] = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><head><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\" /><style id=\"Classeur1_16681_Styles\"></style></head><body><div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\"><table x:str border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

    $xls[] = "<tr><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';

    foreach ($data As $o) {
        $line = array();
        foreach ($keynames AS $k => $v) {

            $line[] = $o[$k];
        }

        $xls[] = '<tr class="xl2216681 nowrap"><td>' . implode("</td><td>", $line) . '</td></tr>';
    }

    $xls[] = '<table></div></body></html>';

    $xls = join("\r\n", $xls);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition: attachment; filename="' . $name . '.xls"');
    header("Content-Transfer-Encoding: binary");

    die(mb_convert_encoding($xls, 'UTF-8', 'UTF-8'));
}

/**
 * 获取数组的随机组合情况
 *
 * @param $a
 * @return array
 */
function get_all_data($a) {
    $data = array();
    foreach ($a as $row) {
        foreach ($a as $val) {
            foreach ($a as $v) {
                if ($row != $val && $row != $v && $val != $v) {
                    $data[] = $row . $val . $v;
                }
            }
        }
    }
    return $data;
}
