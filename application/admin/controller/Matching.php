<?php
namespace app\admin\controller;

use think\Request;

class Matching extends Base
{
    /**
     * 配套区齐套检查
     */
    public function matching_inspect(){
       return $this->fetch('matching_inspect');
    }
    /**
     * 配套区齐套检查post
     */
    public function matching_post(){
        $specification_type = 'YE3-200L-4';//$_POST['specification_type'];
        //$number = $_POST['number'];
        $number = 3;
        $this->assign('specification_type',$specification_type);
        $this->assign('number',$number);
        return $this->fetch('matching_post');
    }

    /**
     *总装件
     * @return false|string
     */

    public function getQrcodeRecord(){
        $data = Request::instance()->post();
        /**
         * 查出所有有关电机型号的所有总装部件 数量
         * 根据总装部件显示子集部件 数量 再根据子集查找下个子集的子集
         * 如果总装的一个部件 数量为0，就只能把这个为0的部件所有子集显示在总装部件里，以此类推
         * 根据数据判断各个部件的数量是否齐套
         * 如果不齐套 显示为红色并显示每个部件所在位置
         * 不够测用未出库（入库的）未入库（没有入库记录0）
         */
        $data1 = array();
        if(!empty($data['specification_type'])){
            $product = db('products')->where('specification_type',$data['specification_type'])->find();
            $number = 3;
            $data1 = $this->isQrcodeRecord($product['id'],$number,15);
        }
        $arrs=$this->arrs($data1);
        return json_encode($arrs,JSON_UNESCAPED_UNICODE);
    }

    public function arrs($data){
        foreach ($data as $k=>$v){
            $arr['id'] = $v['id'];
            $arr['pId'] = $v['pid'];
            $ss = db('inventory')->where('inventory_code',$v['base_code'])->find();
            if(!empty($ss)){
                $arr['name']= $ss['inventory_name'].'('.$v['specification_type'].')('.$v['counts'].')';
            }else{
                $arr['name']= $v['inventory_class_name'].'('.$v['specification_type'].')('.$v['counts'].')';
            }
            $arr['open']=false;
            if(!empty($v['children'])){
                $arr['children'] = $this->arrs($v['children']);
            }
            $arrs[] = $arr;
        }
        return $arrs;
    }

    /**
     * 部件
     * @return mixed
     */
    public function qrcodeRecord(){
        $id = input('id');
        $product = db('qrcode_record')->where('id',$id)->find();
        $product_list = db('qrcode_record')
            ->alias('a')
            ->join('tf_process b','b.id = a.process_flow_id')
            ->where('a.base_code',$product['base_code'])
            ->where('a.pid',0)
            ->where('a.specification_type',$product['specification_type'])
            ->select();
        return json_encode($product_list);
    }

    public function isQrcodeRecord($id,$number,$process_flow_id=''){
        $product_list = db('products')->where('pid',$id)->select();
        foreach ($product_list as $k=>$v){
            $where=array();
            if(!empty($process_flow_id)){
                $where['process_flow_id'] = $process_flow_id;
            }
            $data1[]= db('qrcode_record')
                ->distinct('base_code')
                ->field('*,count(base_code) as counts')
                ->where('base_code',$v['inventory_code'])
                ->where('pid','0')
                ->where($where)
                ->group('base_code')->find();
            $product_lists = db('products')->where('pid',$v['id'])->find();
            if(!empty($product_lists)&&$data1[$k]['counts']<$number){
                $data1[]['children'] = $this->isQrcodeRecord($v['id'],$number);
            }
        }
        return $data1;
    }
}