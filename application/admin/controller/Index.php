<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\common\Erweima;
class Index extends Controller
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
    public function getTree($data,$pid=0,$level=0){
        static $arr=array();
        foreach($data as $key=>$value){
            if($value['pid'] == $pid){
                $value['level']=$level;     //用来作为在模版进行层级的区分
                $arr[] = $value;            //把内容存进去
                $this->getTree($data,$value['id'],$level+1);    //回调进行无线递归
            }
        }
        return $arr;

    }

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