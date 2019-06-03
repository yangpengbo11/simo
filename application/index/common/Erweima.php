<?php


namespace app\index\common;
use app\index\common\GetTime;

class Erweima
{
    public function index(Array $lists){
        header('Content-Type: image/png');
        $date=new GetTime();
        $time=$date->ts_time();
        vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $data="";
        foreach ($lists as $list){
            $data=$data.$list;
        }
        $data=$data.$time;

        
        $level=3;
        $size=4;
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object = new \QRcode();
        ob_end_clean();//关键
        $code = urlencode($data);
        //生成二维码图片
        return $object->png($code, false, $errorCorrectionLevel, $matrixPointSize, 2);



    }
}