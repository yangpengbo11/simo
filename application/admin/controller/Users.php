<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Users extends Base
{
    /**
     * 人员档案列表
     */
    public function account_list(){

        $data = db('account')->order('id','asc')->select();
        $this->assign('data',$data);
        return $this->fetch('account_list');
    }
    /**
     * 增加人员信息
     */
    public function account_add(){
        $this->assign('data','');
        return $this->fetch('account_post');
    }
    /**
     * 修改人员信息
     */
    public function account_edit(){
        $id = input('id');
        $data = db('account')->where(['id'=>$id])->find();
        $this->assign('data',$data);
        return $this->fetch('account_post');
    }
    /**
     * 增加/修改人员信息post提交
     */
    public function account_post(){
        if(!empty($_POST['id'])){//修改
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name']
            );
            $res = db('account')->where('id',$_POST['id'])->update($arr);
            //print_r(db('account')->getLastSql());die;
            if($res){
                $this->success('编辑成功.', 'Users/account_list');
            }else{
                $this->error('编辑失败！');
            }
        }else{//增加
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('account')->insert($arr);
            if($res){
                $this->success('添加成功.', 'Users/account_list');
            }else{
                $this->error('添加失败！');
            }
        }
    }

    /**
     * 登录账号列表
     */
    public function accountNumber_list(){
        $list = db('user_login')
            ->alias('a')
            ->join('tf_account b','b.id = a.personnel_id')
            ->field('b.*,a.login_id,a.account_name')
            ->select();
        $this->assign('list',$list);
        return $this->fetch('number_list');
    }

    /**
     * 增加人员信息
     */
    public function number_add(){
        $this->assign('data','');
        return $this->fetch('number_post');
    }

    /**
     * 修改人员信息
     */
    public function number_edit(){
        $id = input('id');
        $data = db('user_login')
            ->alias('a')
            ->join('tf_account b','b.id = a.personnel_id')
            ->field('b.*,a.login_id,a.account_name')
            ->where(['a.id'=>$id])
            ->select();
        $this->assign('data',$data);
        return $this->fetch('account_post');
    }

    /**
     * 增加/修改人员账号信息post提交
     */
    public function number_post(){
        if(!empty($_POST['id'])){//修改
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name']
            );
            $res = db('account')->where('id',$_POST['id'])->update($arr);
            //print_r(db('account')->getLastSql());die;
            if($res){
                $this->success('编辑成功.', 'Users/account_list');
            }else{
                $this->error('编辑失败！');
            }
        }else{//增加
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('account')->insert($arr);
            if($res){
                $this->success('添加成功.', 'Users/account_list');
            }else{
                $this->error('添加失败！');
            }
        }
    }
}
