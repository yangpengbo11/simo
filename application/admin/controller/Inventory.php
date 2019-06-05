<?php


namespace app\admin\controller;
use think\Controller;

class Inventory extends Base
{
   public function inventory_list(){
      $data=db('inventory')->order('inventory_id','asc')->select();
      $this->assign('data',$data);
      return $this->fetch();
   }

   public function inventory_add(){
       $unit=db('unit')->where(['states'=>'1'])->select();
       $this->assign('unit',$unit);
       $this->assign('data','');
       return $this->fetch('inventory_post');
   }
}