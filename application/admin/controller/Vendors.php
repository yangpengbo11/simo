<?php


namespace app\admin\controller;

class Vendors extends Base
{
    /**
     * 供货商管理
     */
    public function vendor_list(){
        $list = db('vendor')->select();
        $this->assign('list',$list);
        return $this->fetch('vendor_list');
    }

    /**
     * 添加供货商页面
     */
    public function vendor_add(){
        $this->assign('data','');
        return $this->fetch('vendor_post');
    }

    /**
     * 编辑供货商页面
     */
    public function vendor_edit(){
        $id = input('id');
        $vend = db('vendor')->where('vendor_id',$id)->find();
        $this->assign('data',$vend);
        return $this->fetch('vendor_post');
    }

    /**
     * 添加/编辑post提交
     */
    public function vendor_post(){

        if(empty($_POST['vendor_id'])){
            $data = array(
                'vendor_code' => $_POST['vendor_code'],
                'vendor_name' => $_POST['vendor_name'],
                'company_addr'=> $_POST['company_addr'],
                'create_time' => date('Y-m-d H:i:s',time())
            );
            $vend = db('vendor')->insert($data);
            if($vend){
                $this->success('添加成功.', 'Vendors/vendor_list');
            }else{
                $this->error('添加失败！');
            }
        }else{
            $data = array(
                'vendor_code' => $_POST['vendor_code'],
                'vendor_name' => $_POST['vendor_name'],
                'company_addr'=> $_POST['company_addr'],
            );
            $vend = db('vendor')->where('vendor_id',$_POST['vendor_id'])->update($data);
            if($vend){
                $this->success('编辑成功.', 'Vendors/vendor_list');
            }else{
                $this->error('编辑失败！');
            }
        }
    }
}