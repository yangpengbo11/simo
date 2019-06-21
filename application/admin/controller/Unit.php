<?php


namespace app\admin\controller;
use think\Request;

class Unit extends Base
{
    public function unit_list(){

      $data=db('unit')->order('unit_id','asc')->select();

       $this->assign('data',$data);
       return $this->fetch('unit_list');

    }

    public function unit_add(){
        $this->assign('data','');
        return $this->fetch('unit_post');
    }


    public function unit_edit(){
        $id=input('id');
        $data=db('unit')->where(['unit_id'=>$id])->find();
        $this->assign('data',$data);
        return $this->fetch('unit_post');
    }


    public function unit_post(){
        if(empty($_POST['unit_id'])){
            $data=Request::instance()->post();
            $data['create_time']=date('Y-m-d H:i:s');
            $data['states']=1;
            $res=db('unit')->insert($data);
            if($res){
                $this->success('添加成功','unit/unit_list');
            }else{
                $this->error('添加失败 ','unit/unit_list');
            }
        }else{
            $data=Request::instance()->post();
            $res=db('unit')->update($data);
            if($res){
                $this->success('修改成功', 'unit/unit_list');
            }else{
                $this->error('修改失败','unit/unit_list');
            }

        }

    }











}