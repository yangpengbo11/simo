<?php


namespace app\admin\controller;

use think\Request;
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

   public function inventory_edit(){
       $id=input('id');
       $data=db('inventory')->where(['inventory_id'=>$id])->find();
       $unit=db('unit')->where(['states'=>'1'])->select();
       //var_dump($data);
      $this->assign('unit',$unit);
       $this->assign('data',$data);
       return $this->fetch('inventory_post');
   }


   public function inventory_post(){
       if(empty($_POST['inventory_id'])){
           $user=session('users');
           //var_dump($user['account_name']);
           $data=Request::instance()->post();
           $data['create_person']=$user['account_name'];
           $data['sts_date']=date('Y-m-d H:i:s');
           $res=db('inventory')->insert($data);
           if($res){
               $this->success('添加成功', 'inventory/inventory_list');
           }else{
               $this->error('添加失败','inventory/inventory_list');
           }
       }else{
           $user=session('users');
           $data=Request::instance()->post();
           $data['modify_person']=$user['account_name'];
           $data['modify_date']=date('Y-m-d H:i:s');
           $res=db('inventory')->update($data);
           if($res){
               $this->success('修改成功', 'inventory/inventory_list');
           }else{
               $this->error('修改失败','inventory/inventory_list');
           }
       }

   }

   public function inventory_detail(){
       $id=input('id');
       $data=db('inventory')->where(['inventory_id'=>$id])->find();
       $this->assign('data',$data);
       return $this->fetch('inventory_detail');
   }














}