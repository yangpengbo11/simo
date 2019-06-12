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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
            $this->error(

                '登陆失败！');
        }
    }

    public function login_out(){
        session('users','');
        $this->success('','Login/login');
    }

//    public function newCode(){
//        // 配置验证码的显示方式和内容
//        $config =    [
//                // 验证码位数
//        'length'      =>    4,
//        // 验证码过期时间
//        'expire'      =>    300,
//        // 是否添加杂点
//        'useNoise' => true,
//        // 是否画混淆曲线
//        'useCurve' => false,
//        // 是否使用背景图片
//        'useImgBg' => false,
//        // 使用字体
//        'fontttf' => '3.ttf'
//        ];
//        $captcha = new Captcha($config);
//        return $captcha->entry();
//     }
}
