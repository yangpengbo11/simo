<?php
namespace app\admin\controller;


use app\admin\common\Erweima;
class Index extends Base
{
    /**
     * 后台登录页面
     * @return mixed
     */
    public function index()
    {
        $data = session('users');
        $menu = db('menus')->order('id','asc')->select();
        $menu = $this->getTree($menu);
        //print_r($data);die;
        $this->assign('menu',$menu);
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    public function welcome(){
        return $this->fetch('welcome');
    }

    //无限极递归


    public function qrcode(){

        $erwei=new Erweima();
        $lists=array(
            'base_code'=>"simo",
            'specification_type'=>"14558788",
            'figure_number'=>"12554",
        );
        $erwei->qrcode($lists,10,2);

    }

}
