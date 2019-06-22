<?php


namespace app\admin\controller;
use app\admin\common\Erweima;
use think\Request;
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

   public function qrcord_post(){
        return $this->fetch();
   }

    public function qrcord_add(){
        $lists=Request::instance()->post();
        $lists['base_code']="simo";
        $erweima =new Erweima();
        //$lists=array("base_code"=>"simo");
        $erweima->qrcode($lists,10,3);
        $this->success('生成二维码成功','/admin/qrcord/qrcord_list');
    }
}