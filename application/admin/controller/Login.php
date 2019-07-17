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
            $res = $this->alert('登陆成功！','/admin/Index/index',6,5);
            return $res;
        }else{
            $res = $this->alert('登陆失败,用户密码不匹配！','login',6,5);
            return $res;
        }
    }

    public function login_out(){
        session('users','');
        $res = $this->alert('退出登录成功！','/admin/Login/login',6,5);
        return $res;
    }

    function alert($msg='',$url='',$icon='',$time=3){
        $str='<script type="text/javascript" src="http://192.168.18.21/lib/jquery/1.9.1/jquery.min.js"></script><script type="text/javascript" src="http://192.168.18.21/lib/layer/2.4/layer.js"></script>';//加载jquery和layer
        $str.='<script>$(function(){layer.msg("'.$msg.'",{icon:'.$icon.',time:'.($time*1000).'});setTimeout(function(){self.location.href="'.$url.'"},2000)});</script>';//主要方法
        return $str;
    }
}
