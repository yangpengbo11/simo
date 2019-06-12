<?php


namespace app\admin\controller;
use think\Request;

class Stock extends Base
{
   public function stock_list(){
     $data=db('stock')
         ->alias('a')
         ->join('inventory b','a.inventory_id=b.inventory_id')
         //->field('b.inventory_code,b.inventory_name,b.specification_type')
         ->join('warehouse c','c.id = a.warehouse_id')
         ->field('b.inventory_code,b.inventory_name,b.specification_type,c.name,a.*')
         ->select();
       $this->assign('data',$data);
       return $this->fetch('stock_list');
   }


   //添加，修改方法
   public function stock_comm(){
       $inventory=db('inventory')
           ->field('inventory_id,inventory_name')
           ->select();
       $warehouse=db('warehouse')
           ->field('id,name')
           ->select();
       $id=input('id');
       /*var_dump($id);
       die();*/
       if(is_null($id)){
           //添加
           $this->assign('data','');
       }else{
           //修改
           $data=db('stock')->where(['stock_id'=>$id])->find();
           $this->assign('data',$data);
       }
       //下拉框数据
       $this->assign('inv',$inventory);
       $this->assign('war',$warehouse);
       return $this->fetch('stock_post');
   }

   public function stock_post(){
       if(empty($_POST['stock_id'])){
           $data=Request::instance()->post();
           $data['create_time']=date('Y-m-d H:i:s');
           $res=db('stock')->insert($data);
           if($res){
               $this->success('添加成功','stock/stock_list');
           }else{
               $this->error('添加失败','stock/stock_list');
           }
       }else{
           $data=Request::instance()->post();
           $res=db('stock')->update($data);
           if($res){
               $this->success('修改成功','stock/stock_list');
           }else{
               $this->error('修改失败','stock/stock_list');
           }
       }
   }




}