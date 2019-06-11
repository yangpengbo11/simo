<?php


namespace app\admin\controller;

class Products extends Base
{
    /**
     *半成品bom列表
     */
     public function half_products_list(){
         $list = db('half_products')->select();
         $this->assign('list',$list);
         return $this->fetch('half_products_list');
     }
    /**
     *添加半成品bom
     */
     public function half_products_add(){
         $this->assign('data','');
        return $this->fetch('half_products_post');
     }
    /**
     *编辑半成品bom
     */
     public function half_products_edit(){
         $id = input('id');
         $res = db('half_products')->where('id',$id)->find();
         $this->assign('data',$res);
         return $this->fetch('half_products_post');
     }
    /**
     *添加/编辑半成品bom post提交
     */
     public function half_process_post(){
         if(empty($_POST['id'])){
            $data = array(
                'process_name'=>$_POST['process_name'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('half_products')->insert($data);
            if($res){
                $this->success('添加成功.', 'Products/half_products_list');
            }else{
                $this->error('添加失败！');
            }
         }else{
             $data = array(
                 'process_name'=>$_POST['process_name']
             );
             $res = db('half_products')->where('id',$_POST['id'])->update($data);
             if($res){
                 $this->success('编辑成功.', 'Products/half_products_list');
             }else{
                 $this->error('编辑失败！');
             }
         }
     }





}