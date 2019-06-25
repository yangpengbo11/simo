<?php
namespace app\admin\controller;

class Users extends Base
{
    /**
     * 人员档案列表
     */
    public function account_list(){

        $data = db('account')->order('id','asc')->select();
        $this->assign('data',$data);
        return $this->fetch('account_list');
    }
    /**
     * 增加人员信息
     */
    public function account_add(){
        $this->assign('data','');
        return $this->fetch('account_post');
    }
    /**
     * 修改人员信息
     */
    public function account_edit(){
        $id = input('id');
        $data = db('account')->where(['id'=>$id])->find();
        $this->assign('data',$data);
        return $this->fetch('account_post');
    }
    /**
     * 增加/修改人员信息post提交
     */
    public function account_post(){

        if(!empty($_POST['Job_number'])){
            $arr = db('account')->where('Job_number',$_POST['Job_number'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('人员工号已存在！重新输入','account_add',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('人员工号已存在！','account_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }
        if(!empty($_POST['job_name'])){
            $arr = db('account')->where('job_name',$_POST['job_name'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('人员名称已存在！','account_add',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('人员名称已存在！','account_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }
        if(!empty($_POST['id'])){//修改
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name']
            );
            $res = db('account')->where('id',$_POST['id'])->update($arr);
            //print_r(db('account')->getLastSql());die;
            if($res){
                //$this->success('编辑成功.', 'Users/account_list');
                $res = $this->alert('编辑成功.','account_list',6,3);
                return $res;
            }else{
                $res = $this->alert('编辑失败！','account_edit/id/'.$_POST['id'],5,3);
                return $res;
            }
        }else{//增加
            $arr = array(
                'Job_number'=>$_POST['Job_number'],
                'job_name'=>$_POST['job_name'],
                'create_time'=>date('Y-m-d H:i:s',time())
            );
            $res = db('account')->insert($arr);
            if($res){
                $res = $this->alert('添加成功.', 'account_list',6,3);
                return $res;
            }else{
                $res = $this->alert('添加失败！','account_add',5,3);
                return $res;
            }
        }
    }

    /**
     * 登录账号列表
     */
    public function accountNumber_list(){
        $list = db('user_login')
            ->alias('a')
            ->join('tf_account b',' a.personnel_id = b.id')
            ->field('b.*,a.login_id,a.account_name')
            ->select();
        foreach ($list as $k=>$v){
            if(empty($v['process_id'])){
                $v['process_id'] = 0;
            }
            $res = db('process_matching')
                ->alias('a')
                ->join('tf_process b','a.process_id = b.id')
                ->join('tf_inventory_class c','a.inventory_class_id = c.inventory_class_id')
                ->field('a.inventory_class_id,a.process_id,b.process_name,c.inventory_class_name')
                ->where('a.login_id',$v['login_id'])
                ->find();
            if(!empty($res)){
                $list[$k]['inventory_class_id'] =  $res['inventory_class_id'];
                $list[$k]['inventory_class_name'] =  $res['inventory_class_name'];
                $list[$k]['process_id'] =  $res['process_id'];
                $list[$k]['process_name'] = $res['process_name'];
            }else{
                $list[$k]['inventory_class_name'] =  '';
                $list[$k]['process_name'] ='';
            }
        }
        $this->assign('list',$list);
        return $this->fetch('number_list');
    }

    /**
     * 增加人员信息
     */
    public function number_add(){
        $res = db('roles')->select();
        $this->assign('data','');
        $this->assign('role_id','');
        $this->assign('roles',$res);
        $list = db('process')->select();
        $this->assign('process',$list);
        return $this->fetch('number_post');
    }

    /**
     * 修改人员信息
     */
    public function number_edit(){
        $id = input('id');
        $data =  db('user_login')
            ->alias('a')
            ->join('tf_account b','b.id = a.personnel_id')
            ->join('tf_process_matching c','c.login_id = a.login_id')
            ->join('tf_inventory_class d','c.inventory_class_id = d.inventory_class_id')
            ->field('b.*,a.*,c.inventory_class_id,c.process_id,c.types,d.inventory_class_code')
            ->where(['a.login_id'=>$id])
            ->find();
        $u_r = db('user_roles')->where('login_id', $id)->find();
        $res = db('roles')->select();
        $list = db('process')->select();

        $this->assign('process',$list);
        $this->assign('data',$data);
        $this->assign('role_id',$u_r['role_id']);
        $this->assign('roles',$res);
        return $this->fetch('number_post');
    }

    /**
     * 增加/修改人员账号信息post提交
     */
    public function number_post(){

        if(!empty($_POST['Job_number'])){
            $arr = db('account')->where('Job_number',$_POST['Job_number'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('人员工号已存在！重新输入','number_add',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('人员工号已存在！','number_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }

        if(!empty($_POST['account_name'])){
            $arr = db('user_login')->where('account_name',$_POST['account_name'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('账号名称已存在！重新输入','number_add',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('账号名称已存在！','number_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }
        if(!empty($_POST['id'])){//修改
            //print_r($_POST);die;
            $job_number = $_POST['Job_number'];
            $res = db('account')->where('Job_number',$job_number)->find();
            $arr = array(
                'account_name'=>$_POST['account_name'],
                'password'=>md5($_POST['password']),
                'personnel_id'=>$res['id'],
                'create_time'=>date('Y-m-d H:i:s',time())
            );

            $login_id = db('user_login')->where('login_id',$_POST['id'])->update($arr);
            if($login_id==1) {
                $user = array(
                    'role_id' => $_POST['role_id'],
                    'create_time' => date('Y-m-d H:i:s', time())
                );
                $u_r = db('user_roles')->where('login_id', $_POST['id'])->update($user);
                //print_r($u_r);die;
                if(!empty($_POST['inventory_class_code'])){
                    $inventory_class = db('inventory_class')->where('inventory_class_code',$_POST['inventory_class_code'])->find();
                    $pro = db('process_matching')->where('login_id',$_POST['id'])->find();
                    if(empty($pro)){
                        $process = array(
                            'login_id'=>$_POST['id'],
                            'types'=>$_POST['types'],
                            'inventory_class_id'=>$inventory_class['inventory_class_id'],
                            'process_id'=>$_POST['process_id'],
                            'create_time'=>date('Y-m-d H:i:s',time())
                        );
                        db('process_matching')->insert($process);
                    }else{
                        $process = array(
                            'types'=>$_POST['types'],
                            'inventory_class_id'=>$inventory_class['inventory_class_id'],
                            'process_id'=>$_POST['process_id']
                        );
                        db('process_matching')->where('login_id',$_POST['id'])->update($process);
                    }
                }
                //print_r(db('account')->getLastSql());die;
                if ($u_r) {
                    $res = $this->alert('编辑成功！','accountNumber_list',6,3);
                    return $res;
                } else {
                    $res = $this->alert('编辑失败！','accountNumber_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }else{
                $res = $this->alert('编辑失败！','accountNumber_edit/id/'.$_POST['id'],5,3);
                return $res;
            }
        }else{//增加
            $job_number = $_POST['Job_number'];
            $res = db('account')->where('Job_number',$job_number)->find();
            $arr = array(
                'account_name'=>$_POST['account_name'],
                'password'=>md5($_POST['password']),
                'personnel_id'=>$res['id'],
                'create_time'=>date('Y-m-d H:i:s',time())
            );
            $login_id = db('user_login')->insertGetId($arr);
            if($login_id){
                $user = array(
                    'role_id'=>$_POST['role_id'],
                    'login_id'=>$login_id,
                    'create_time'=>date('Y-m-d H:i:s',time())
                );
                $u_r = db('user_roles')->insert($user);
                if(!empty($_POST['inventory_class_code'])) {
                    $inventory_class = db('inventory_class')->where('inventory_class_code',$_POST['inventory_class_code'])->find();
                    $process = array(
                        'login_id' => $login_id,
                        'types' => $_POST['types'],
                        'inventory_class_id' => $inventory_class['inventory_class_id'],
                        'process_id' => $_POST['process_id'],
                        'create_time'=>date('Y-m-d H:i:s',time())
                    );
                    db('process_matching')->insert($process);
                }
                if($u_r){
                    $res = $this->alert('添加成功！','accountNumber_edit',6,3);
                    return $res;
                }else{
                    $res = $this->alert('添加失败！','accountNumber_add',5,3);
                    return $res;
                }
            }else{
                $res = $this->alert('添加失败！','accountNumber_add',5,3);
                return $res;
            }
        }
    }

    /**
     * 角色列表
     */
    public function role_list(){
        $list = db('roles')->select();
        $this->assign('list',$list);
        return $this->fetch('role_list');
    }

    /**
     * 角色增加页面
     * @return mixed
     */
    public function role_add(){

        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        $this->assign('data','');
        $this->assign('datas',$data);
        return $this->fetch('role_post');
    }


    /**
     * 角色修改页面
     * @return mixed
     */
    public function role_edit(){
        $id = input('id');
        $role = db('roles')->where('id',$id)->find();
        $data = db('menus')->order('id','asc')->select();
        $data = $this->getTree($data);
        $this->assign('datas',$data);
        $this->assign('data',$role);
        return $this->fetch('role_post');
    }

    /**
     * 增加/修改角色post提交
     */
    public function role_post(){
        //$_POST
        if(!empty($_POST['role_name'])){
            $arr = db('roles')->where('role_name',$_POST['role_name'])->find();
            if(empty($_POST['id'])){
                if(!empty($arr)){
                    $res = $this->alert('角色名称已存在！重新输入','role_add',5,3);
                    return $res;
                }
            }else{
                if($_POST['id']!=$arr['id']&&!empty($arr)){
                    $res = $this->alert('角色名称已存在！','role_edit/id/'.$_POST['id'],5,3);
                    return $res;
                }
            }
        }
        if(empty($_POST['id'])) {
            $data = array(
                'role_name' => $_POST['role_name'],
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $role = db('roles')->insertGetId($data);
            if ($role) {
                foreach ($_POST['roles'] as $vo) {
                    $data1 = array(
                        'role_id' => $role,
                        'menus_id' => $vo,
                        'create_time' => date('Y-m-d H:i:s', time())
                    );
                    db('roles_authority')->insert($data1);
                }
                $res = $this->alert('添加成功！','role_list',6,3);
                return $res;
            } else {
                $res = $this->alert('添加失败！','role_add',5,3);
                return $res;
            }
        }else{
            $data = array(
                'role_name' => $_POST['role_name'],
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $role = db('roles')->where('id',$_POST['id'])->update($data);
            if ($role) {
                db('roles_authority')->where('role_id',$_POST['id'])->delete();
                foreach ($_POST['roles'] as $vo) {
                    $data1 = array(
                        'role_id' => $_POST['id'],
                        'menus_id' => $vo,
                        'create_time' => date('Y-m-d H:i:s', time())
                    );
                    db('roles_authority')->insert($data1);
                }
                $res = $this->alert('编辑成功！','role_list',6,3);
                return $res;
            }else{
                $sel = db('roles_authority')->where('role_id',$_POST['id'])->select();
                if(empty($sel)){
                    foreach ($_POST['roles'] as $vo) {
                        $data1 = array(
                            'role_id' => $_POST['id'],
                            'menus_id' => $vo,
                            'create_time' => date('Y-m-d H:i:s', time())
                        );
                        db('roles_authority')->insert($data1);
                    }
                    $res = $this->alert('编辑成功！','role_list',6,3);
                    return $res;
                }
            }
        }
    }

//    /**
//     * 搜索显示数据
//     * @return
//     */
//    public function vague(){
//
//        $vague = input('vague');
//        $list = $this->getVague('inventory_class','inventory_class_name',$vague);
//        return json($list);
//    }
}
