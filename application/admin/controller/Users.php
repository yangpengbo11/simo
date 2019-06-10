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
        $res = db('roles')->select();
        $this->assign('data','');
        $this->assign('roles',$res);
        return $this->fetch('number_post');
    }

    /**
     * 修改人员信息
     */
    public function number_edit(){
        $id = input('id');
        $data = $list = db('user_login')
            ->alias('a')
            ->join('tf_account b','b.id = a.personnel_id')
            ->field('b.*,a.*')
            ->where(['login_id'=>$id])
            ->find();
        $u_r = db('user_roles')->where('login_id', $id)->find();
        $res = db('roles')->select();
        //print_r($u_r);die;
        $this->assign('data',$data);
        $this->assign('role_id',$u_r['role_id']);
        $this->assign('roles',$res);
        return $this->fetch('number_post');
    }

    /**
     * 增加/修改人员账号信息post提交
     */
    public function number_post(){
        if(!empty($_POST['id'])){//修改
            //print_r($_POST);die;
            $job_number = $_POST['Job_number'];
            $res = db('account')->where('Job_number',$job_number)->find();
            $arr = array(
                'account_name'=>$_POST['account_name'],
                'password'=>md5($_POST['password']),
                'personnel_id'=>$res['id'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $login_id = db('user_login')->where('login_id',$_POST['id'])->update($arr);
            if($login_id) {
                $user = array(
                    'role_id' => $_POST['role_id'],
                    'create_time' => date('Y-d-m H:i:s', time())
                );
                $u_r = db('user_roles')->where('login_id', $_POST['id'])->update($user);

                //print_r(db('account')->getLastSql());die;
                if ($u_r) {
                    $this->success('编辑成功.', 'Users/accountNumber_list');
                } else {
                    $this->error('编辑失败！');
                }
            }else{
                $this->error('编辑失败！');
            }
        }else{//增加
                $job_number = $_POST['Job_number'];
                $res = db('account')->where('Job_number',$job_number)->find();
                $arr = array(
                    'account_name'=>$_POST['account_name'],
                    'password'=>md5($_POST['password']),
                    'personnel_id'=>$res['id'],
                    'create_time'=>date('Y-d-m H:i:s',time())
                );
                $login_id = db('user_login')->insertGetId($arr);
                if($login_id){
                    $user = array(
                        'role_id'=>$_POST['role_id'],
                        'login_id'=>$login_id,
                        'create_time'=>date('Y-d-m H:i:s',time())
                    );
                    $u_r = db('user_roles')->insert($user);
                    if($u_r){
                        $this->success('添加成功.', 'Users/accountNumber_list');
                    }else{
                        $this->error('添加失败！');
                    }
                }else{
                    $this->error('添加失败！');
                }
        }
    }

    /**
     * 角色列表
     */
    public function role_list(){
        $list = db('roles')->select();
        $this->assign('list',$list);
        return $this->fetch('role_list');
    }

    /**
     * 角色增加页面
     * @return mixed
     */
    public function role_add(){

        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        $this->assign('data','');
        $this->assign('datas',$data);
        return $this->fetch('role_post');
    }


    /**
     * 角色修改页面
     * @return mixed
     */
    public function role_edit(){
        $id = input('id');
        $role = db('roles')->where('id',$id)->find();
        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        $this->assign('datas',$data);
        $this->assign('data',$role);
        return $this->fetch('role_post');
    }

    /**
     * 增加/修改角色post提交
     */
    public function role_post(){
        //$_POST
        if(empty($_POST['id'])) {
            $data = array(
                'role_name' => $_POST['role_name'],
                'create_time' => date('Y-d-m H:i:s', time())
            );
            $role = db('roles')->insertGetId($data);
            if ($role) {
                foreach ($_POST['roles'] as $vo) {
                    $data1 = array(
                        'role_id' => $role,
                        'menus_id' => $vo,
                        'create_time' => date('Y-d-m H:i:s', time())
                    );
                    db('roles_authority')->insert($data1);
                }
                $this->success('添加成功！', 'Users/role_list');
            } else {
                $this->error('添加失败！');
            }
        }else{
            $data = array(
                'role_name' => $_POST['role_name'],
                'create_time' => date('Y-d-m H:i:s', time())
            );
            $role = db('roles')->where('id',$_POST['id'])->update($data);
            if ($role) {
                db('roles_authority')->where('role_id',$_POST['id'])->delete();
                foreach ($_POST['roles'] as $vo) {
                    $data1 = array(
                        'role_id' => $_POST['id'],
                        'menus_id' => $vo,
                        'create_time' => date('Y-d-m H:i:s', time())
                    );
                    db('roles_authority')->insert($data1);
                }
                $this->success('编辑成功！', 'Users/role_list');
            }else{
                $sel = db('roles_authority')->where('role_id',$_POST['id'])->select();
                if(empty($sel)){
                    foreach ($_POST['roles'] as $vo) {
                        $data1 = array(
                            'role_id' => $_POST['id'],
                            'menus_id' => $vo,
                            'create_time' => date('Y-d-m H:i:s', time())
                        );
                        db('roles_authority')->insert($data1);
                    }
                    $this->success('编辑成功！', 'Users/role_list');
                }
            }
        }
    }
}
