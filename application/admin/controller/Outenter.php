<?php


namespace app\admin\controller;
use think\Request;
use app\admin\common\Erweima;
use app\admin\common\GetTime;
class Outenter extends Base
{
    //出入库记录查询
    public function outenter_out($type=1){
        $data=db('out_enter_record')
            ->alias('a')
            ->join('vendor c','c.vendor_id = a.supplier_id','left')
            ->join('inventory d','d.inventory_id=a.materiel_id','left')
            ->field('c.vendor_name,a.*,d.inventory_name,d.inventory_code,d.specification_type')
            ->where(['a.types'=>$type])
            ->select();
        $this->assign('data',$data);
        return $this->fetch('outenter_list');
    }

    //添加入库记录 查询库房，供应商
    public function outenter_addi(){
        $this->assign('data','');
        return $this->fetch('outenter_posti');
    }

   //入库操作
   public function outenter_posti(){
       $data=Request::instance()->post();
       $erweima=$data['materiel_coding'];
       //判断二维码是否存在
       $erweimas=$this->isexistence($erweima);
       if($erweimas){
           //判断二维码是否是未入库状态
           if($erweimas['process_flow_id']==1){
               $code=substr($erweima,0,stripos($erweima,'*'));
               $inventory_id=db('inventory')->where(['inventory_code'=>$code])->field('inventory_id')->find();
               $data['materiel_id']=$inventory_id['inventory_id'];
               $data['types']=1;
               $data['batch_mark']=date('Ymd');
               $data['operator']=session('users')['account_name'];
               $data['create_time']=date('Y-m-d H:i:s');
               $res=db('out_enter_record')->insert($data);
               if($res){
                   db('stock')
                       ->where(['inventory_id'=>$inventory_id['inventory_id']])
                       ->setInc('last_quantity');
                   $qrcode_record=array(
                       'process_flow_id'=>2,
                       'create_time'=>date('Y-m-d H:i:s',time())
                   );
                   db('qrcode_record')->where('qrcode_content',$erweima)->update($qrcode_record);
                   return $this->alert('操作成功','/admin/outenter/outenter_addi',6);
               }else{
                   return $this->alert('入库失败','/admin/outenter/outenter_addi',5);
               }
           }else{
               return $this->alert('此物料已出库','/admin/outenter/outenter_addi',5);
           }
       }else{
          return $this->alert('此二维码不存在','/admin/outenter/outenter_addi',5);
       }
   }

   //跳转到出库操作页面
   public function outenter_addo(){
       return $this->fetch('outenter_posto');
   }

    //出库操作
    public function outenter_posto(){
        $data=Request::instance()->post();
        $erweima=$data['materiel_coding'];
        //判断二维码是否存在
        $erweimas=$this->isexistence($erweima);
        if($erweimas){
            //判断二维码是否是未出库状态
            if($erweimas['process_flow_id']==2){
                $re=db('out_enter_record')->where(['materiel_coding'=>$erweima,'types'=>1])->find();
                unset($re['id']);
                $re['types']=2;
                $re['operator']=session('users')['account_name'];
                $re['create_time']=date('Y-m-d H:i:s');
                $res=db('out_enter_record')->insert($re);
                if($res){
                    db('stock')
                        ->where(['inventory_id'=>$re['materiel_id']])
                        ->setDec('last_quantity');
                    if(isset($data['machnumber'])){
                        $qrcode_record=array(
                            'process_flow_id'=>3,
                            'create_time'=>date('Y-m-d H:i:s',time()),
                            'machnumber'=>$data['machnumber']
                        );
                        db('qrcode_record')->where('qrcode_content',$erweima)->update($qrcode_record);
                    }else{
                        $qrcode_record=array(
                            'process_flow_id'=>3,
                            'create_time'=>date('Y-m-d H:i:s',time())

                        );
                        db('qrcode_record')->where('qrcode_content',$erweima)->update($qrcode_record);
                    }
                    //添加二维码操作记录
                    $inventory_code=substr($erweima,0,strpos($erweima,'*'));
                    $inventory_class_code=db('inventory')->where('inventory_code',$inventory_code)->find();
                    $inventory_class_id=db('inventory_class')->where('inventory_class_code',$inventory_class_code['inventory_class_code'])->find();
                    $ress=db('process_flow')
                        ->alias('a')
                        ->join('process b','a.process_id=b.id')
                        ->where(['inventory_class_id'=>$inventory_class_id['inventory_class_code']])
                        ->select();
                    foreach ($ress as $v){
                        //$qrcode=array(['qrcode_content'=>$erweima['qrcode_content'],'process_name'=>$v['process_name'],'operation_states'=>0,'create_time'=>date('Y-m-d H:i:s'),'states'=>1]);
                        db('qrcode')
                            ->data(['qrcode_content'=>$erweima,'process_name'=>$v['process_name'],'operation_states'=>0,'create_time'=>date('Y-m-d H:i:s'),'states'=>1])
                            ->insert();
                    }
                    return $this->alert('操作成功','/admin/outenter/outenter_addo',6);
                }else{
                    return $this->alert('出库失败','/admin/outenter/outenter_addo',5);
                }
            }else{
                return $this->alert('此物料已出库','/admin/outenter/outenter_addo',5);
            }
        }else{
            return $this->alert('此二维码不存在','/admin/outenter/outenter_addo',5);
        }
    }

    public function outenter_erweima(){
        $id=input('id');
        $data=db('qrcode_record')->where(['access_id'=>$id])->select();
        if(empty($data)){
            $this->error('没有二维码','/admin/outenter/outenter_out');
        }else{
            $this->assign('data',$data);
            return $this->fetch('outenter_erweima');
        }
    }

    //跳转到生成流转码的页面
    public function outenter_fuma(){
        return $this->fetch();
    }
   //入库之前生成流转码
    public function outenter_creweima(){
        $data=Request::instance()->post();
        $inventory=db('inventory')->where(['inventory_code'=>$data['materiel_coding']])->find();
        if($inventory){
            $erwei=new Erweima();
            $ti=new GetTime();
            $t=$ti->ts_time();
            $c=substr($t,0,11);
            $d=substr($t,11);
            for($i=1;$i<=$data['number'];$i++){
                $a=$c.($d+$i);
                $lists=array('base_code'=>$data['materiel_coding'],'roam'=>$a,'specification_type'=>$inventory['specification_type'],'figure_number'=>$inventory['dwg_code'],'inventory_class_code'=>$inventory['inventory_class_code'],'inventory_class_name'=>$inventory['inventory_name'],'process_flow_id'=>1);
                $erwei->qrcode($lists,5,1);
            }
            return $this->alert('操作成功','/admin/outenter/outenter_fuma',6);
        }else{
           return $this->alert('系统没有此物料的档案，请先添加档案','/admin/inventory/inventory_add',5);
        }
    }


//ajax查询加工图号
public function selmachnumber(){
    $materiel_coding=$_POST['materiel_coding'];
   // 58XD.013.2590*YE3-200L-4*190715135535138
    $inventory_code=substr($materiel_coding,0,strpos($materiel_coding,'*'));
    $data=db('inventory_machnumber')->where(['inventory_code'=>$inventory_code])->select();
    return json_encode($data,JSON_UNESCAPED_UNICODE);
}

//添加供应商查询
public function outenter_gys($ids){
    // $arr= explode(",",$ids);
    $data=db('vendor')->select();
    $this->assign('data',$data);
    $this->assign('ids',$ids);
    return $this->fetch('outenter_gysp');
}

public function outenter_sgys(){
    $data=Request::instance()->post();
    $ids=explode(",",$data['ids']);
    $ini['id'] = array('in',$ids);
    $res = db('out_enter_record')->where($ini)->setField('supplier_id',$data['supplier_id']);
    if($res){
        return $this->alert('操作成功','/admin/outenter/outenter_out',6);
    }else{
        return $this->alert('操作失败','/admin/outenter/outenter_out',5);
    }
}





}