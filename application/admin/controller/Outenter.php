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
            ->field('b.name,c.vendor_name,a.*')
            ->where(['types'=>$type])
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
            if($data['types']==1){
                db('stock')
                    ->where(['inventory_id'=>$ids['inventory_id']])
                    ->setDec('last_quantity',$data['number']);

                if($fas==1){
                    $lists=array('base_code'=>$data['materiel_coding']);
                    //print_r($lists['base_code']);die;
                    $erwei=new Erweima();
                    $erwei->qrcode($lists,10,1);
                }
            }else{
                db('stock')
                    ->where(['inventory_id'=>$ids['inventory_id']])
                    ->setInc('last_quantity',$data['number']);
            }
           $this->success('操作成功','outenter/outenter_out');
        }else{
            $this->error('操作失败','outenter/outenter_out');
        }
    }













}