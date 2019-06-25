<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
    /**
     * 后台登录页面
     * @return mixed
     */
    public function login()
    {
        return $this->fetch('Login');
    }

    /**
     * 后台登录post提交
     * @return mixed
     */
    public function login_post(){
        $where['account_name'] = $_POST['name'];
        $where['password'] = md5($_POST['password']);
       // print_r(md5(123456));
        $users = db('user_login');
        $as = $users->where($where)->find();
        //print_r(session('users'));die;
        if($as){
            session('users',$as);
            $this->success('登陆成功', 'Index/index');
        }else{
            $this->error('登陆失败,用户密码不匹配！');
        }
    }

    public function login_out(){
        session('users','');
        $this->success('','Login/login');
    }
}
