<?php
namespace app\admin\controller;

use think\Controller;
use think\db;

class Menu extends Base
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
        $this->assign('menu','');
        $this->assign('data',$data);
        return $this->fetch('add_menu');
    }




    /**
     * post增加修改菜单
     * @return mixed
     */
    public function menu_post(){
        if(!empty($_POST['id'])){
            $arr = [
                'pid'=>$_POST['pid'],
                'menu_name'=>$_POST['menu_name'],
                'types'=>$_POST['types'],
                'links'=>$_POST['links']
            ];
            $res = db('menus')->where('id',$_POST['id'])->update($arr);
            //print_r(db('menus')->getLastSql());die;
            if($res){
                $this->success('编辑成功.', 'Menu/menu_list');
            }else{
                $this->error('编辑失败！');
            }
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
     *
     */
    public function edit_menu(){
        $id = input('id');
        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        $menu = db('menus')->where(['id'=>$id])->find();
        $this->assign('data',$data);
        $this->assign('menu',$menu);
        return $this->fetch('add_menu');
    }


}
