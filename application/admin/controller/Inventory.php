<?php


namespace app\admin\controller;

use think\Request;
class Inventory extends Base
{
   public function inventory_list(){
    // $data=db('inventory')->order('inventory_id','asc')->select();
       $data=db('inventory')
           ->alias('a')
           ->join('warehouse b','a.storehouse_id=b.id','left')
           ->join('unit c','a.unit_code=c.unit_code','left')
           ->order('inventory_id','desc')
           ->select();
      $this->assign('data',$data);
      return $this->fetch();
   }

   public function inventory_add(){
       $unit=db('unit')->where(['states'=>'1'])->select();
       $warehouse=db('warehouse')->select();
       $this->assign('warehouse',$warehouse);
       $this->assign('unit',$unit);
       $this->assign('data','');
       return $this->fetch('inventory_post');
   }

   public function inventory_edit(){
       $id=input('id');
       $data=db('inventory')->where(['inventory_id'=>$id])->find();
       $unit=db('unit')->where(['states'=>'1'])->select();
       //var_dump($data);
       $warehouse=db('warehouse')->select();
       $this->assign('warehouse',$warehouse);
      $this->assign('unit',$unit);
       $this->assign('data',$data);
       return $this->fetch('inventory_post');
   }

   public function inventory_post(){
       $user=session('users');
       $data=Request::instance()->post();

       if(!empty($data['machnumber'])){
           $machnumbers=$data['machnumber'];
       }
       unset($data['machnumber']);
       if(empty($_POST['inventory_id'])){
           $data['create_person']=$user['account_name'];
           $data['sts_date']=date('Y-m-d H:i:s');
           $res=db('inventory')->insert($data);
           if($res){
               if(isset($machnumbers)){
                   $machnumber= explode(",", $machnumbers);
                   foreach ($machnumber as $val){
                       $data1=array('inventory_code'=>$data['inventory_code'],'machnumber'=>$val,'create_time'=>date('Y-m-d H:i:s'),'state'=>1);
                       db('inventory_machnumber')->insert($data1);
                   }
               }
               return $this->alert('添加成功','/admin/inventory/inventory_list',6);
           }else{
               return $this->alert('添加失败','/admin/inventory/inventory_list',5);
           }
       }else{
           $data['modify_person']=$user['account_name'];
           $data['modify_date']=date('Y-m-d H:i:s');
           $res=db('inventory')->update($data);
           if($res){
               return $this->alert('修改成功','/admin/inventory/inventory_list',6);
           }else{
               return $this->alert('修改失败','/admin/inventory/inventory_list',6);

           }
       }
   }

//详情页
   public function inventory_detail(){
       $id=input('id');
       $data=db('inventory')
           ->alias('a')
           ->join('warehouse b','a.storehouse_id=b.id','left')
           ->join('unit c','a.unit_code=c.unit_code','left')
           ->where(['inventory_id'=>$id])
           ->find();
       //var_dump($data);die();
       $data1=db('inventory_machnumber')->where(['inventory_code'=>$data['inventory_code'],'state'=>1])->select();
       $this->assign('data',$data);
       $this->assign('data1',$data1);
       return $this->fetch('inventory_detail');
   }

    //添加加工图号传存货编码参数
   public function inventory_mpnumber($code){
       $this->assign('code',$code);
       return $this->fetch('inventory_mpnumberpost');
   }

   //添加加工图号
   public function inventory_mpnumberpost(){
       $data=Request::instance()->post();
       $data['create_time']=date('Y-m-d H:i:s');
       $data['state']=1;
       $res=db('inventory_machnumber')->insert($data);
       if($res){
           return $this->alert('添加成功','/admin/inventory/inventory_list',6);
       }else{
           return $this->alert('添加失败','/admin/inventory/inventory_list',5);
       }
   }

   public function inventory_mpnumberedit(){

   }


}