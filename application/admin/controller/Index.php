<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
       // $view = new View();
        $this->assign('domain',$this->request->url(true));
        return $this->fetch('index');
//        $view = new View();
//        return $view->fetch('index');
    }
}
