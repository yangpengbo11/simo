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
        //查找用户角色
        $role = db('user_roles')->field('role_id')->where('login_id',$data['login_id'])->select();
        $menus = db('roles_authority')->field('menus_id')->where('role_id',$role[0]['role_id'])->order('id','asc')->select();
        $menu = array();
        foreach ($menus as $v){
            $menu[].=$v['menus_id'];
        }
        $menu = db('menus')->where('id','in',$menu)->order('id','asc')->select();
        $menu = $this->getTree($menu);
        $role_name = db('roles')->where('id',$role[0]['role_id'])->find();
        $this->assign('role',$role_name);
        $this->assign('menu',$menu);
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    public function welcome(){
        return $this->fetch('welcome');
    }




    /*public function qrcode(){

        $erwei=new Erweima();
        $lists=array(
            'base_code'=>"simo",
            'specification_type'=>"14558788",
            'figure_number'=>"12554",
        );
        $erwei->qrcode($lists,10,2);

    }*/

}
