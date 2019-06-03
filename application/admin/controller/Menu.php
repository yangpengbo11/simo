<?php
namespace app\admin\controller;

use think\Controller;

class Menu extends Controller
{
    /**
     * 菜单列表
     * @return mixed
     */
    public function menu_list()
    {
        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        //print_r($data);die;
        $this->assign('data',$data);
        return $this->fetch('menu_list');
    }

    /**
     * 增加菜单
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add_menu(){
        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        //print_r($data);die;
        $this->assign('data',$data);
        return $this->fetch('add_menu');
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
     * post增加修改菜单
     * @return mixed
     */
    public function menu_post(){
        if(!empty($_POST['id'])){

        }else{
            $arr = array(
                'pid'=>$_POST['pid'],
                'menu_name'=>$_POST['menu_name'],
                'types'=>$_POST['types'],
                'links'=>$_POST['links'],
                'create_time'=>date('Y-d-m H:i:s',time())
            );
            $res = db('menus')->insert($arr);
            if($res){
                $this->success('添加成功.', 'Menu/menu_list');
            }else{
                $this->error('添加失败！');
            }
        }
    }
    /**
     * 修改菜单
     * @return mixed
     */
    public function edit_menu(){
        return $this->fetch('edit_menu');
    }

}
