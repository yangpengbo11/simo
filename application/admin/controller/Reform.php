<?php
namespace app\admin\controller;

use think\Request;

class Reform extends Base
{
    //改制复新
    public function reform_index(){
        //要改制的成品码或半成品码
        return $this->fetch('reform_index');
    }

    public function reform_list(){

        $content = input('qrcode_content');
        $id = db('qrcode_record')->where('qrcode_content',$content)->find();
        $list = db('qrcode_record')->where('pid',$id['id'])->select();
        $this->assign('list',$list);
        return $this->fetch('reform_list');

    }


    public function reform_post()
    {
        /**
         * 获取更改电机父级所有零部件
         * 判断是否更改此部件
         * 0，不变，零部件父级ID改为新成品ID
         * 1，改制，跟换新部件，只需对旧部件进行跟踪并去掉父级ID
         * 2，复新，在原部件添加工序并去掉父级ID
         */
        $arr_id =$_POST['id'];
        $content = $_POST['simo_product'];
        if(empty($content)){
            $this->error('没有新成品码无法改制复新！');
        }
        $id = db('qrcode_record')->where('qrcode_content',$content)->find();
        foreach ($arr_id as $k=>$v){
            $reform1 = $_POST['reform'.$v];
            if($reform1==0){
                $data = array('pid' => $id['id']);
                $id = db('qrcode_record')->where('id',$v)->update($data);
            }
            if($reform1==1){
                /**
                 * 旧部件跟踪数据
                 * 旧部件表增加一条跟踪数据，ID，旧成品ID，新成品ID，旧部件ID，旧部件名称，去向，状态
                 * 旧部件去向已完成，时去掉旧部件。
                 */
                $reforms = $_POST['reforms'.$v];
                $list = db('qrcode_record')->where('id',$v)->find();
                $arr = array(
                    'used_products_id'=> $list['pid'],
                    'new_products_id' => $id['id'],
                    'used_qrcode_red' => $list['id'],
                    'inventory_class_name' => $list['inventory_class_name'],
                    'direction' => $reforms,
                    'states' =>0
                );
                db('parts_direction')->insert($arr);
                $data = array('pid' => $id['id']);
                $id = db('qrcode_record')->where('id',$v)->update($data);
            }
            if($reform1==2){
                $data = array('pid' => 0);
                db('qrcode_record')->where('id',$v)->update($data);
                //加复新工序操作数据，添加部件操作工序流转数据，复新成活
                $find = db('qrcode_record')->where('id',$v)->find();
                $arrs = array(
                    'qrcode_content' => $find['qrcode_content'],
                    'operator' => '',
                    'process_name'  => '复新成活',
                    'operation_time'=>'',
                    'operation_states' =>0,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'states' => 1
                );
               $id = db('qrcode')->insert($arrs);
            }
        }
        if($id){
            $res = $this->alert('改制复新成功！','/admin/Reform/reform_index',6,3);
            return $res;
        }else{
            $res = $this->alert('改制复新失败！','/admin/Reform/reform_index',5,3);
            return $res;
        }
    }

    public function reform_detail(){
        $id=input('id');
        $data=db('qrcode_record')->where(['id'=>$id])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
}