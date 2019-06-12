<?php


namespace app\admin\controller;

class Working extends Base
{
    /**
     *工序列表
     */
     public function working_list(){
         $list = db('process')->select();
         $this->assign('list',$list);
         return $this->fetch('working_list');
     }

     public function working_add(){
         $this->assign('data','');
        return $this->fetch('working_post');
     }

     public function working_edit(){
         $id = input('id');
         $res = db('process')->where('id',$id)->find();
         $this->assign('data',$res);
         return $this->fetch('working_post');
     }

     public function working_post(){
         if(empty($_POST['id'])){
            $data = array(
                'process_name'=>$_POST['process_name'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('process')->insert($data);
            if($res){
                $this->success('添加成功.', 'Working/working_list');
            }else{
                $this->error('添加失败！');
            }
         }else{
             $data = array(
                 'process_name'=>$_POST['process_name']
             );
             $res = db('process')->where('id',$_POST['id'])->update($data);
             if($res){
                 $this->success('编辑成功.', 'Working/working_list');
             }else{
                 $this->error('编辑失败！');
             }
         }
     }





}