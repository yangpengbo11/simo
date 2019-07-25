<?php
namespace app\admin\controller;
use think\Controller;
class Sweepcode extends Controller
{
    public function lists(){

        return $this->fetch('sweepcode_list');
    }
    /**
     * 扫码显示bom
     * @return mixed
     */
    public function index()
    {
        $qrcode_content = $_POST['qrcode_content'];
        $simobom = db('qrcode_record')->where('qrcode_content',$qrcode_content)->select();
        $list = $this->SweepcodeData($simobom);
        return json_encode($list,JSON_UNESCAPED_UNICODE);
    }

    static $arrs=array();
    static $i=0;
    public function SweepcodeData($data){
        foreach($data as $key=>$value){
            $arr['id'] = $value['id'];
            $arr['pId'] = $value['pid'];
            $ss = db('inventory')->where('inventory_code',$value['base_code'])->find();
            if(!empty($ss)){
                $arr['name']= $ss['inventory_name'].'('.$value['specification_type'].')'.$value['machnumber'];
            }else{
                $arr['name']= $value['inventory_class_name'].'('.$value['specification_type'].')'.$value['machnumber'];
            }
            $arr['open']=false;
            $simobom = db('qrcode_record')->where('pid',$arr['id'])->select();
            $arrs[] = $arr;
            if(!empty($simobom)){
                $arrs[]['children'] =  $this->SweepcodeData($simobom);    //回调进行无线递归
            }
        }
        return $arrs;
    }

    public function find(){
       $id = input('id');
        $simobom = db('qrcode_record')->where('id',$id)->select();
        $res = $this->list_qrcode($id,$simobom);
        $data = array();
        foreach ($res as $vo){//编号，物料名称，图号，型号，工序名称，操作人,操作时间
            $arr1 = db('out_enter_record')->where('materiel_coding',$vo['qrcode_content'])->select();
            $data2 = array();
            foreach ($arr1 as $k=>$v1){
                $data2[$k]['base_code'] = $vo['base_code'];
                $data2[$k]['inventory_class_name'] = $vo['inventory_class_name'];
                $data2[$k]['figure_number'] = $vo['figure_number'];
                $data2[$k]['specification_type'] = $vo['specification_type'];
                $data2[$k]['process_name'] = $v1['types']==1?'入库':'出库';
                $data2[$k]['operators'] = $v1['operator'];
                $data2[$k]['operation_time'] = $v1['create_time'];
            }
            $data = array_merge($data,$data2);

            $arr = db('qrcode')->where('qrcode_content',$vo['qrcode_content'])->select();
            $data1 = array();
            foreach ($arr as $k=>$v){
              $data1[$k]['base_code'] = $vo['base_code'];
              $data1[$k]['inventory_class_name'] = $vo['inventory_class_name'];
              $data1[$k]['figure_number'] = $vo['figure_number'];
              $data1[$k]['specification_type'] = $vo['specification_type'];
              $data1[$k]['process_name'] = $v['process_name'];
              $data1[$k]['operators'] = $v['operator'];
              $data1[$k]['operation_time'] = $v['operation_time'];
            }
            $data = array_merge($data,$data1);
        }
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public function list_qrcode($id,$array = array()){
        $list = db('qrcode_record')->where('pid',$id)->select();
        $arr = array_merge($array,$list);
        foreach ($list as $v){
            $arrs = db('qrcode_record')->where('pid',$v['id'])->select();
            if(count($arrs)!=0){
                $arr = $this->list_qrcode($v['id'],$arr);
            }
        }
        return $arr;
    }
}
