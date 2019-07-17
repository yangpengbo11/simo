<?php
namespace app\admin\controller;

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
        if(!empty($_POST['menu_name'])){
            $arr = db('menus')->where('menu_name',$_POST['menu_name'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('菜单名称已存在!','add_menu',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('菜单名称已存在!','edit_menu/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }
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
                $res = $this->alert('编辑成功.','menu_list',6,3);
                return $res;
            }else{
                $res = $this->alert('编辑失败！','edit_menu/id/'.$_POST['id'],5,3);
                return $res;
            }
        }else{
            $arr = array(
                'pid'=>$_POST['pid'],
                'menu_name'=>$_POST['menu_name'],
                'types'=>$_POST['types'],
                'links'=>$_POST['links'],
                'create_time'=>date('Y-m-d H:i:s',time())
            );
            $res = db('menus')->insert($arr);
            if($res){
                $res = $this->alert('添加成功.','menu_list',6,3);
                return $res;
            }else{
                $res = $this->alert('添加失败！','add_menu',5,3);
                return $res;
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
