<?php
namespace app\admin\controller;

use think\Request;

class Products extends Base
{
    /**
     *成品bom列表
     */
    public function products_list(){
        $list = db('products')->select();
        $this->assign('list',$list);
        return $this->fetch('products_list');
    }

    /**
     * 添加/编辑成品bom post提交
     */
     public function products_post(){
         if(empty($_POST['id'])){
             $data = array(
                 'inventory_code'=>$_POST['inventory_code'],
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
                 'inventory_code'=>$_POST['inventory_code'],
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
     * 所有配套的成品
     */
     public function products_json(){
         $res = db('products')->order('id','asc')->select();
         $arr=$this->getTreeData($res);
         return json_encode($arr,JSON_UNESCAPED_UNICODE);
     }

     public function getcode(){
         $res = db('products')->order('id','asc')->select();
         $arr=$this->getTreeData($res);
         return json_encode($arr,JSON_UNESCAPED_UNICODE);
     }

    public function find(){
        $id=$_POST['id'];
        $data=db('products')->where(['id'=>$id])->find();
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public function submit(Request $request){
        $data=$request->post();
        $id=$data['id'];
        if(empty($id)){
            $res=db('products')->insert($data);
            if($res){
                $this->success('添加成功','Products/products_list');
            }else{
                $this->error('添加失败','Products/products_list');
            }
        }else{
            $res=db('products')->where('id',$id)->update($data);
            if($res){
                $this->success('修改成功','Products/products_list');
            }else{
                $this->error('修改失败','Products/products_list');
            }
        }
    }

    public function getTreeData($data){
        $arrs=array();
        foreach($data as $res){
            $arr=array();
            $arr['id']=$res['id'];
            $arr['pId']=$res['pid'];
            $arr['name']='('.$res['inventory_code'].')'.$res['product_name'];
            $arr['open']=false;
            array_push($arrs, $arr);
        }
        return $arrs;
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