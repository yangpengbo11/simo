<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    //检查是否登录
    public function _initialize()
    {
        if(!session('users')){
            $this->error('请先登录！',url('/admin/Login/login'));
        }

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

}