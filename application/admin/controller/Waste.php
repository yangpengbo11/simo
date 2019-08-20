<?php


namespace app\admin\controller;
use think\Request;

class Waste extends Base
{

    public function waste_r(){
        return $this->fetch('waste_post');
    }

    public function waste_post(){
        $qrcode_content=$_POST['materiel_coding'];
        $erweimas=$this->isexistence($qrcode_content);
        if($erweimas){
            if($erweimas['process_flow_id']==20 || $erweimas['process_flow_id']==21){
                $users = session('users');
                $data1 = array(
                    'operator'         => $users['account_name'],
                    'operation_time'   => date('Y-m-d H:i:s',time()),
                    'operation_states'=>1,
                    'states'=>5,
                    'qrcode_content'=>$qrcode_content,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'process_name'=>'入废料库'
                );
                $insert_qr = db('qrcode')->insert($data1);
                $data2 = array(
                    'process_flow_id'=>22,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'waste_states'=>1
                );
                db('qrcode_record')->where('qrcode_content',$qrcode_content)->update($data2);
                return $this->alert('操作成功','/admin/waste/waste_r',6);
            }else{
                return $this->alert('你输入的二维码不是废料','/admin/waste/waste_r',5);
            }
        }else{
            return $this->alert('你输入的二维码不存在','/admin/waste/waste_r',5);
        }
    }

   public function waste_select(){
        $process_flow_id=array(20,21,22);
        $data=db('qrcode_record')->where('process_flow_id','in',$process_flow_id)->select();
        $this->assign('data',$data);
        return $this->fetch();
   }

   public function waste_detail(){
        $qrcode_content=input('qrcode_content');
        //print_r($qrcode_content);die();
        $data=db('out_enter_record')->where('materiel_coding',$qrcode_content)->select();
        $data1=db('qrcode')->where('qrcode_content',$qrcode_content)->select();
        //print_r($data1);die();
        $this->assign('data',$data);
       $this->assign('data1',$data1);
        return $this->fetch();
   }



   public function time_warning(){
        $d=10;
        $data=db('qrcode_record')
            ->where('process_flow_id','neq',16)
            ->whereTime('create_time','<','-'.$d. 'days')->select();
        /*foreach ($data as $v){
            print_r($v['create_time']);
            echo "<br>";
        }*/
       $this->assign('data',$data);
       return $this->fetch();
   }

}