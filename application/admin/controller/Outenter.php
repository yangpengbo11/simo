<?php


namespace app\admin\controller;
use think\Request;
use app\admin\common\Erweima;
class Outenter extends Base
{
    //出入库记录查询
    public function outenter_out($type=1){
        $data=db('out_enter_record')
            ->alias('a')
            ->join('warehouse b','a.storehouse_id=b.id')
            ->join('vendor c','c.vendor_id = a.supplier_id')
            ->join('inventory d','d.inventory_code=a.materiel_coding')
            ->field('b.name,c.vendor_name,a.*,d.inventory_name,d.specification_type')
            ->where(['a.types'=>$type])
            ->select();
        //$data=db('out_enter_record')->where(['types'=>$type])->select();
        $this->assign('data',$data);
        return $this->fetch('outenter_list');
    }

    //添加查询库房，供应商
    public function outenter_add($type){
        $warehouse=db('warehouse')->select();
        $vendor=db('vendor')->select();
        $this->assign('data','');
        $this->assign('type',$type);
        $this->assign('warehouse',$warehouse);
        $this->assign('vendor',$vendor);
        return $this->fetch('outenter_post');
    }

    //修改查询库房，供应商
    public function outenter_edit(){
        $id=input('id');
        $data=db('out_enter_record')->where(['id'=>$id])->find();
        $warehouse=db('warehouse')->select();
        $vendor=db('vendor')->select();
        $this->assign('data',$data);
        $this->assign('type','');
        $this->assign('warehouse',$warehouse);
        $this->assign('vendor',$vendor);
        return $this->fetch('outenter_post');
    }

    public function outenter_post(){

        $data=Request::instance()->post();

        if($data['types']=='出库'){
            $data['types']=1;
        }elseif ($data['types']=='入库'){
            $data['types']=2;
        }
        $data['create_time']=date('Y-m-d H:i:s');
        $code=$data['materiel_coding'];
        $ids=db('inventory')->field('inventory_id')->where(['inventory_code'=>$code])->find();
        /*var_dump($ids);
        die();*/
        $fas=$data['fals'];
        unset($data['snumber']);
        unset($data['fals']);
        $res=db('out_enter_record')->insert($data);
        if($res){
            $access_id=db('out_enter_record')->getLastInsID();
            if($data['types']==1){
                db('stock')
                    ->where(['inventory_id'=>$ids['inventory_id']])
                    ->setDec('last_quantity',$data['number']);
                if($fas==1){
                    $code=$data['materiel_coding'];
                    $res=db('inventory')
                        ->where(['inventory_code'=>$code])
                        ->find();
                    /*var_dump($res);
                    die();*/
                    $lists=array('base_code'=>$data['materiel_coding']);
                    //print_r($lists['base_code']);die;
                    $erwei=new Erweima();
                    for($a=0;$a<$data['number'];$a++){
                        $id=$erwei->qrcode($lists,10,1,$access_id);

                        $re=db('inventory_class')
                            ->where(['inventory_class_code'=>$res['inventory_class_code']])
                            ->find();

                        $ress=db('process_flow')
                            ->alias('a')
                            ->join('process b','a.process_id=b.id')
                            ->where(['inventory_class_id'=>$re['inventory_class_id']])
                            ->select();
                        /*var_dump($ress);
                        break;
                        die();*/
                        $erweima=db('qrcode_record')
                            ->where(['id'=>$id])
                            ->find();

                        foreach ($ress as $v){
                            //$qrcode=array(['qrcode_content'=>$erweima['qrcode_content'],'process_name'=>$v['process_name'],'operation_states'=>0,'create_time'=>date('Y-m-d H:i:s'),'states'=>1]);
                            db('qrcode')
                                ->data(['qrcode_content'=>$erweima['qrcode_content'],'process_name'=>$v['process_name'],'operation_states'=>0,'create_time'=>date('Y-m-d H:i:s'),'states'=>1])
                                ->insert();
                        }
                    }

                }
            }else{
                db('stock')
                    ->where(['inventory_id'=>$ids['inventory_id']])
                    ->setInc('last_quantity',$data['number']);
            }

           $this->success('操作成功','/admin/outenter/outenter_add/type/'.$data['types'],2);
        }else{
            $this->error('操作失败','/admin/outenter/outenter_add/type/'.$data['types']);
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










}