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
         $pid = db('half_products')->select();
         $stock = db('inventory_class')->order('inventory_class_id','asc')->select();
         $this->assign('pids',$pid);
         $this->assign('stock',$stock);
         return $this->fetch('half_products_post');
     }
    /**
     *编辑半成品bom
     */
     public function half_products_edit(){
         $id = input('id');
         $res = db('half_products')->where('id',$id)->find();
         $pid = db('half_products')->select();
         $stock = db('inventory_class')->order('inventory_class_id','asc')->select();
         $this->assign('data',$res);
         $this->assign('pids',$pid);
         $this->assign('stock',$stock);
         return $this->fetch('half_products_post');
     }
    /**
     *添加/编辑半成品bom post提交
     */
     public function half_products_post(){
         if(empty($_POST['id'])){
             $data = array(
                 'inventory_class_id'=>$_POST['inventory_class_id'],
                 'half_product_name'=>$_POST['half_product_name'],
                 'pid'=>$_POST['pid'],
                 'create_time'=>date('Y-m-d H:i:s',time())
             );
             $res = db('half_products')->insert($data);
             if($res){
                 $this->success('添加成功.', 'Products/half_products_list');
             }else{
                 $this->error('添加失败！');
             }
         }else{
             $data = array(
                 'inventory_class_id'=>$_POST['inventory_class_id'],
                 'half_product_name'=>$_POST['half_product_name'],
                 'pid'=>$_POST['pid']
             );
             $res = db('half_products')->where('id',$_POST['id'])->update($data);
             if($res){
                 $this->success('编辑成功.', 'Products/half_products_list');
             }else{
                 $this->error('编辑失败！');
             }
         }
     }

    /**
     *成品bom列表
     */
    public function products_list(){
        $list = db('products')->select();
        $this->assign('list',$list);
        return $this->fetch('products_list');
    }

    /**
     *编辑成品bom
     */
    public function products_edit(){

        $id = input('id');
        $res = db('products')->where('id',$id)->find();
        $pid = db('products')->select();
        $stock = db('inventory_class')->order('inventory_class_id','asc')->select();
        $this->assign('data',$res);
        $this->assign('stock',$stock);
        $this->assign('pids',$pid);
        return $this->fetch('products_post');
    }

    /**
     *添加成品bom
     */
    public function products_add(){
         $this->assign('data','');
         $pid = db('products')->select();
         $stock = db('inventory_class')->order('inventory_class_id','asc')->select();
         //print_r($pid);print_r($stock);die;
         $this->assign('pids',$pid);
         $this->assign('stock',$stock);
         return $this->fetch('products_post');
     }

    /**
     * 添加/编辑成品bom post提交
     */
     public function products_post(){
         if(empty($_POST['id'])){
             $data = array(
                 'inventory_class_id'=>$_POST['inventory_class_id'],
                 'product_name'=>$_POST['product_name'],
                 'pid'=>$_POST['pid'],
                 'create_time'=>date('Y-m-d H:i:s',time())
             );
             $res = db('products')->insert($data);
             if($res){
                 $this->success('添加成功.', 'Products/products_list');
             }else{
                 $this->error('添加失败！');
             }
         }else{
             $data = array(
                 'inventory_class_id'=>$_POST['inventory_class_id'],
                 'product_name'=>$_POST['product_name'],
                 'pid'=>$_POST['pid']
             );
             $res = db('products')->where('id',$_POST['id'])->update($data);
             if($res){
                 $this->success('编辑成功.', 'Products/products_list');
             }else{
                 $this->error('编辑失败！');
             }
         }
     }

    /**
     * 搜索显示数据
     * @return
     */
    public function vague(){

        $vague = input('vague');
        $list = $this->getVague('inventory','inventory_name|inventory_code',$vague);
        return json($list);
    }
}