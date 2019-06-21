<?php


namespace app\admin\controller;


class Qrcord extends Base
{

    public function qrcord_list(){
        $data=db('qrcode_record')->order('id desc')->select();
        $this->assign("data",$data);
        return $this->fetch('qrcord_list');

    }


    public function qrcord_detail(){
        $id=input('id');
        $data=db('qrcode_record')->where(['id'=>$id])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
}