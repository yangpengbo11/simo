<?php


namespace app\admin\controller;


class Outenter extends Base
{
    public function outenter_out(){
        $data=db('out_enter_record')->where(['types'=>1])->select();
        $this->assign('data',$data);
        return $this->fetch('outenter_list');
    }
}