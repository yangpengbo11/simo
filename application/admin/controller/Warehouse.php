<?php


namespace app\admin\controller;

use think\Request;
class Warehouse extends Base
{
   public function warehouse_list(){
       $data=db('warehouse')->order('id','asc')->select();
       $this->assign('data',$data);
       return $this->fetch('warehouse_list');
   }

   public function warehouse_add(){
       $this->assign('data','');
       return $this->fetch('warehouse_post');
   }

   public function warehouse_edit(){
       $id=input('id');
       $data=db('warehouse')->where(['id'=>$id])->find();
       $this->assign('data',$data);
       return $this->fetch('warehouse_post');
   }

   public function warehouse_post(){
       if(empty($_POST['id'])){
           $data=Request::instance()->post();
           $data['create_time']=date('Y-m-d H:i:s');
           $res=db('warehouse')->insert($data);
           if($res){
               $this->success('添加成功','warehouse/warehouse_list');
           }else{
               $this->error('添加失败','warehouse/warehouse_list');
           }
       }else{
           $data=Request::instance()->post();

           $res=db('warehouse')->update($data);
           if($res){
               $this->success('修改成功','warehouse/warehouse_list');
           }else{
               $this->error('修改失败','warehouse/warehouse_list');
           }
       }
   }








}