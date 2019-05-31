<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
       // $view = new View();
        $this->assign('domain',$this->request->url(true));
        return $this->fetch('Login');
//        $view = new View();
//        return $view->fetch('index');
    }
}
