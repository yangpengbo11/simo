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

    /**
     * 工序流程配置列表
     */
     public function distribute_list(){
         $list = db('process_flow')
             ->alias('a')
             ->join('tf_inventory_class b','b.inventory_class_id = a.inventory_class_id')
             ->join('tf_process c','c.id = a.process_id')
             ->field('a.*,b.inventory_class_name,c.process_name')
             ->select();
         //print_r($list);die;
         $this->assign('list',$list);
        return $this->fetch('distribute_list');
     }

    /**
     * 工序流程配置添加
     */
    public function distribute_add(){
        $list = db('process')->select();
        $this->assign('data','');
        $this->assign('process',$list);
        return $this->fetch('distribute_post');
    }
    /**
     * 工序流程配置编辑
     */
    public function distribute_edit(){
        $id = input('id');
        $data = db('process_flow')->where('id',$id)->find();
        $list = db('process')->select();
        $this->assign('data',$data);
        $this->assign('process',$list);
        return $this->fetch('distribute_post');
    }

    /**
     * 工序流程配置添加/编辑post提交
     */
    public function distribute_post(){
        if(empty($_POST['id'])){
            $data = array(
                'process_id' => $_POST['process_id'],
                'inventory_class_id' =>$_POST['inventory_class_id'],
                'orders' => $_POST['orders'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('process_flow')->insert($data);
            if($res){
                $this->success('添加成功.', 'Working/distribute_list');
            }else{
                $this->error('添加失败！');
            }
        }else{
            $data = array(
                'process_id' => $_POST['process_id'],
                'inventory_class_id' =>$_POST['inventory_class_id'],
                'orders' => $_POST['orders']
            );
            $res = db('process_flow')->where('id',$_POST['id'])->update($data);
            if($res){
                $this->success('编辑成功.', 'Working/distribute_list');
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
        $list = $this->getVague('inventory_class','inventory_class_name',$vague);
        return json($list);
    }


}