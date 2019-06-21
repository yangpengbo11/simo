<?php

namespace app\admin\controller;
use app\admin\common\Erweima;

class Checkedoperatio extends Base
{
    /**
     * 检验操作
     */
    public function checkedOperatio_add(){
        /**
         * 根据账号登录信息判断检验2生码，1扫码，3绑码
         */
        $users = session('users');
        $res = db('process_matching')->where('login_id',$users['login_id'])->find();
        //print_r($res);die;
        //$res['types'] = 2;
        $this->assign('login',$res);
        return $this->fetch('checkedOperatio_add');
    }

    public function checkedOperatio_post(){
        /**
         * 判断是生码还是扫码
         * 1.扫码：
         * 根据输入编码改变唯一二维码操作表数据的状态
         * 2.生码：
         * 获取生码需要的数据
         * 添加二维码记录生成新二维码之后改变之前部件二维码的父节点
         * 查找二维码操作表对应数据进行更改状态
         * 返回新的二维码
         * 3.绑码
         * 根据输入编码改变二维码记录的唯一数据父节点为成品码唯一Id
         * 时间限制（session控制用户下次访问时间）
         */
        $users = session('users');
        $res = db('process_matching')->where('login_id',$users['login_id'])->find();
        $process = db('process')->where('id',$res['process_id'])->find();
        $data0 = array(
            'operator'         => $users['account_name'],
            'operation_time'   => date('Y-m-d H:i:s',time()),
            'operation_states' => 0
        );
        if($_POST['types']==1){
            $where['qrcode_content'] = $_POST['qrcode_content'];
            $where['process_name'] = $process['process_name'];
            $qrcode = db('qrcode')->where($where)->find();
            if($qrcode['operation_states']==0){
                $data0['operation_states'] = 1;
                $data0['states'] = 1;
                //增加操作时间，操作人员
                db('qrcode')->where($where)->update($data0);
                $this->error('扫码成功！');
            }else{
                $this->error('已操作，不可以再次操作！');
            }
        }
        if($_POST['types']==2){
            $users = session('users');
            $res = db('process_matching')->where('login_id',$users['login_id'])->find();
            //查找基础码
            $inventory = db('inventory_class')->where('inventory_class_id',$res['inventory_class_id'])->find();
            $data = array(
                'base_code'=>$inventory['inventory_class_code'],
                'half_products_id' => $inventory['inventory_class_id'],
                'half_products_name' => $inventory['inventory_class_name'],
                'specification_type'=>$_POST['specification_type'],
                'figure_number'=>$_POST['figure_number']
            );
            $erweima = new Erweima();
            $id = $erweima->qrcode($data,10,2);
            //修改父节点
            foreach ($_POST['qrcode_content'] as $val){
                $data = array(
                    'pid'=>$id
                );
                db('qrcode_record')->where('qrcode_content',$val)->update($data);
            }
            if($id){
                //增加操作时间，操作人员
                $data0['process_name'] = $process['process_name'];
                $data0['operation_states'] = 1;
                $data0['states'] = 2;
                //查找当前部件的所有工序
                $flow = db('process_flow')->where('inventory_class_id',$res['inventory_class_id'])->select();
                foreach ($flow as $v){
                    if($v['flow_type']==1){
                        $process = db('process')->where('id',$v['process_id'])->find();
                        $qrcode1 = db('qrcode_record')->where('id',$id)->find();
                        $v_list = array(
                            'qrcode_content' => $qrcode1['qrcode_content'],
                            'process_name' => $process['process_name'],
                            'operation_states' => 0,
                            'states' =>1
                        );
                        db('qrcode')->insert($v_list);
                    }
                }
                $this->error('生码成功！');
            }else{
                $this->error('已操作，不可以再次操作！');
            }
        }
        //绑码（成品绑码）
        //根据二维码内容区分半成品和成品
        //半成品绑定成品二维码Id
        if($_POST['types']==3){
            $_POST['qrcode_content1'];
            $pieces = explode("*", $_POST['qrcode_content1']);
            if($pieces[0]=='simo'){
                $qrcode1 = db('qrcode_record')->where('qrcode_content',$_POST['qrcode_content1'])->find();
                if($qrcode1){
                    $qrcode2 = db('qrcode_record')->where('qrcode_content',$_POST['qrcode_content2'])->find();
                    if($qrcode2){
                        $data = array(
                            'pid'=>$qrcode1['id']
                        );
                        db('qrcode_record')->where('qrcode_content',$_POST['qrcode_content2'])->update($data);
                        $data0['process_name'] = $process['process_name'];
                        $data0['operation_states'] = 1;
                        $data0['states'] = 3;
                        db('qrcode')->insert($data0);
                        $this->error('绑码成功！');
                    }else{
                        $this->error('被绑码编号不存在！');
                    }
                }else{
                    $this->error('输入绑码编号不存在！');
                }
            }else{
                $this->error('绑码编号不正确！');
            }
        }
    }

    /**
     * 搜索显示数据
     * @return
     */
//    public function vague(){
//
//        $vague = input('vague');
//        $list = $this->getVague('inventory_class','inventory_class_name',$vague);
//        return json($list);
//    }

}