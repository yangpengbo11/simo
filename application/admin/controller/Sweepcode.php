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
                $arr['name']= $ss['inventory_name'].'('.$value['specification_type'].')';
            }else{
                $arr['name']= $value['inventory_class_name'].'('.$value['specification_type'].')';
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
}
