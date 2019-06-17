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

    /**
     * 模糊搜索公共方法
     * @param String $table 表名
     * @param String $likes 搜索字段
     * @param String $vague 搜索
     * @return mixed
     */
    public function getVague($table,$likes,$vague){
        $where[$likes] = ['like', '%' . $vague . '%'];
        $res = db($table)->where($where)->select();
        return $res;
    }

    public function getTreeData($data){
        $arrs=array();
        foreach($data as $res){
            $arr=array();
            $arr['id']=$res['inventory_class_id'];
            $arr['pId']=$res['parent_class_id'];
            $arr['name']='('.$res['inventory_class_code'].')'.$res['inventory_class_name'];
            $arr['open']=false;
            array_push($arrs, $arr);
        }

        return $arrs;

    }

}
