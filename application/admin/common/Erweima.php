<?php


namespace app\admin\common;


class Erweima
{
    public function qrcode(Array $lists,$size,$type){
        //$type  二维码类型  1:流转码  2:半成品码（合成码） 3:成品码  4:库房基础码
        header('Content-Type: image/png');
        $date=new GetTime();
        $time=$date->ts_time();
        vendor('phpqrcode.phpqrcode');
        //拼接二维码内容
        $data="";
       /* if($type==1){
            $data=$data.$lists['base_code']."&".$time;
        }else if($type==1){
            $data=$data.$lists['base_code']."&".$lists['base_code'];
        }*/
        $data=$data.$lists['base_code']."*";
        if(isset($lists['specification_type'])){
            $data=$data.$lists['specification_type']."*";
        }else if(isset($lists['figure_number'])){
            $data=$data.$lists['figure_number']."*";
        }

       /* foreach ($lists as $list){
            $data=$data.$list."*";
        }*/
        $data=$data.$time;


        $level=3;
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object = new \QRcode();
        ob_end_clean();//关键
        //$code = urlencode($data);
        //创建文件夹
        $dir = "images/".$type."/".date("Ymd");
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
        //文件路径
        $filename="images/".$type."/".date("Ymd")."/".time().".png";

        //把二维码信息保存到数据库H

        $lists['roam']=$time;
        $lists['qrcode_content']=$data;
        $lists['types']=$type;
        $lists['links']=$filename;
        $res = db('qrcode_record')->insert($lists);
        //生成二维码图片
         $object->png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        $id = db('qrcode_record')->getLastInsID();
        //返回新增数据的自增主键id;
        return $id;
    }
}