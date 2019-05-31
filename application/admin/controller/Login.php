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
       // $view = new View();
//        $captcha = new Captcha();
//        print_r($captcha);die;
        $this->assign('domain',$this->request->url(true));
        return $this->fetch('Login');
//        $view = new View();
//        return $view->fetch('index');
    }

    /**
     * 后台登录post提交
     * @return mixed
     */
    public function login_post(){
        print_r($_POST);die;
    }

    public function newCode(){
        // 配置验证码的显示方式和内容
        $config =    [
                // 验证码位数
        'length'      =>    4,
        // 验证码过期时间
        'expire'      =>    300,
        // 是否添加杂点
        'useNoise' => true,
        // 是否画混淆曲线
        'useCurve' => false,
        // 是否使用背景图片
        'useImgBg' => false,
        // 使用字体
        'fontttf' => '3.ttf'
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
     }
}
