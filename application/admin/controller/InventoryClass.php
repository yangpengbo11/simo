<?php


namespace app\admin\controller;

use think\Request;
class InventoryClass extends Base
{

   public function inventory_class_list(){
       $data=db('inventory_class')->order('inventory_class_id','asc')->select();
       $this->assign('data',$data);
       return $this->fetch('inventory_class_list');
   }

   public function inventory_calss_add(){
       $this->assign('data','');
       return $this->fetch('inventory_class_post');
   }

   public function inventory_class_edit(){
       $id=input('id');
       $data=db('inventory_class')->where(['inventory_class_id'=>$id])->find();
       $this->assign('data',$data);
       return $this->fetch('inventory_class_post');
   }

   public function inventory_class_post(){
       if(empty($_POST['inventory_class_id'])){
           $data=Request::instance()->post();
           $data['sts_date']=date('Y-m-d H:i:s');
           $res=db('inventory_class')->insert($data);
           if($res){
               $this->success('添加成功','inventory_class/inventory_class_list');
           }else{
               $this->error('添加失败','inventory_class/inventory_class_list');
           }
       }else{
           $data=Request::instance()->post();
           $data['update_Date']=date('Y-m-d H:i:s');
           $res=db('inventory_class')->update($data);
           if($res){
               $this->success('修改成功','inventory_class/inventory_class_list');
           }else{
               $this->error('修改失败','inventory_class/inventory_class_list');
           }
       }

   }






}
