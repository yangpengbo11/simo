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
            ->join('warehouse b','a.storehouse_id=b.id')
            ->join('vendor c','c.vendor_id = a.supplier_id')
            ->join('inventory d','d.inventory_id=a.materiel_id')
            ->field('b.name,c.vendor_name,a.*,d.inventory_name,d.inventory_code,d.specification_type')
            ->where(['a.types'=>$type])
            ->select();
        $this->assign('data',$data);
        return $this->fetch('outenter_list');
    }

    //添加入库记录 查询库房，供应商
    public function outenter_addi(){
        $warehouse=db('warehouse')->select();
        $vendor=db('vendor')->select();
        $this->assign('data','');
        //$this->assign('type',$type);
        $this->assign('warehouse',$warehouse);
        $this->assign('vendor',$vendor);
        return $this->fetch('outenter_posti');
    }

   //入库操作
   public function outenter_posti(){
       $data=Request::instance()->post();
       $erweima=$data['materiel_coding'];
       //判断二维码是否存在
       $erweimas=$this->isexistence($erweima);
       //var_dump($this->isexistence($erweima)); die();
       if($erweimas){
           //判断二维码是否
           //是未入库状态
           if($erweimas['process_name']==null){
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
                   db('qrcode_record')->where('qrcode_content',$erweima)->update(['process_name'=>'入库']);
                   return $this->alert('操作成功','/admin/outenter/outenter_addi',6);
               }else{
                   return $this->alert('
                   入库失败','/admin/outenter/outenter_addi',5);
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
        //var_dump($this->isexistence($erweima)); die();
        if($erweimas){
            //判断二维码是否
            //是未出库状态
            if($erweimas['process_name']=="入库"){
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
                    db('qrcode_record')->where('qrcode_content',$erweima)->update(['process_name'=>'出库','machnumber'=>$data['machnumber']]);
                    return $this->alert('操作成功','/admin/outenter/outenter_addo',6);
                }else{
                    return $this->alert('
                   入库失败','/admin/outenter/outenter_addo',5);
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
                $lists=array('base_code'=>$data['materiel_coding'],'roam'=>$a,'specification_type'=>$inventory['specification_type'],'figure_number'=>$inventory['dwg_code'],'inventory_class_code'=>$inventory['inventory_class_code'],'inventory_class_name'=>$inventory['inventory_name']);
                $erwei->qrcode($lists,5,1);
            }
            $this->success('操作成功','/admin/outenter/outenter_fuma',1);
        }else{
           return
               $this->alert('系统没有此物料的档案，请先添加档案','/admin/inventory/inventory_add',5);
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



}