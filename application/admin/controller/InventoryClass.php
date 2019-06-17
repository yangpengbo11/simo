<?php


namespace app\admin\controller;

use phpDocumentor\Reflection\Types\Object_;
use think\Request;
class InventoryClass extends Base
{

   public function inventory_class_list(){
       //$data=db('inventory_class')->order('inventory_class_id','asc')->select();
      // $this->assign('data',$data);
       //$request=new Request();
       //$arr=$this->getTreeData($data);
       //print_r($arr);die;
       //print_r(json_encode($arr,JSON_UNESCAPED_UNICODE));die;
       return $this->fetch('inventory_class_list');
   }


   public function getjson(){
       $data=db('inventory_class')->order('inventory_class_id','asc')->select();
       // $this->assign('data',$data);
       //$request=new Request();
       $arr=$this->getTreeData($data);
       //print_r($arr);die;
       //print_r(json_encode($arr,JSON_UNESCAPED_UNICODE));die;
       //return $this->fetch('inventory_class_list');
       return json_encode($arr,JSON_UNESCAPED_UNICODE);
   }

   public function find(){
       $id=$_POST['id'];
       $data=db('inventory_class')->where(['inventory_class_id'=>$id])->find();
       return json_encode($data,JSON_UNESCAPED_UNICODE);
   }

    public function getcode(){
        $id=$_POST['id'];
        $code=$_POST['code'];
        $length=strpos($code,")")-1;
        $codes=substr($code,1,$length);

        $data=db('inventory_class')->where(['parent_class_id'=>$id])->order('inventory_class_id','desc')->select();
        $cod="";
        if($data){
            $cod= $data[0]['inventory_class_code']+1;
        }else{
            $cod= $codes."01";
        }
        $arr=array(['parent_class_id'=>$id,'code'=>$cod]);
        return json_encode($arr,JSON_UNESCAPED_UNICODE);
    }


    public function submit(Request $request){
       $data=Request::instance()->post();
       $id=$data['inventory_class_id'];
       if(empty($id)){
           $data['sts_date']=date('Y-m-d H:i:s');
           $res=db('inventory_class')->insert($data);
           if($res){
               $this->success('添加成功','inventory_class/inventory_class_list');
           }else{
               $this->error('添加失败','inventory_class/inventory_class_list');
           }
       }else{
           return "修改";
       }
       //return $data['inventory_class_code'];
       // return json_encode($data,JSON_UNESCAPED_UNICODE);

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
       $data=Request::instance()->post();
       $id=$data['inventory_class_id'];
       if(empty($id)){
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
