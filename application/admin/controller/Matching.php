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
     * 'YE3-200L-4'
     */
    public function matching_post(){
        $number = 3;
        $specification_type = $_POST['specification_type'];
        $number = $_POST['number'];
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
            $product = db('products')->where('pid',0)->where('specification_type',$data['specification_type'])->find();
            $number = $data['number'];
            $data1 = $this->isQrcodeRecord($product['id'],$number,15);

        }
        $arrs=$this->arrs($data1,$number);
        return json_encode($arrs,JSON_UNESCAPED_UNICODE);
    }
    public $arr_s=array();
    public function arrs($data,$number){
        foreach ($data as $k=>$v){
            if(!empty($data[$k])) {
                $arr['id'] = $v['id'];
                $arr['pId'] = $v['pid'];
                if($v['counts']<$number){
                    $arr['name'] = '<div style="color:red">'.$v['inventory_class_name'] . '(' . $v['specification_type'] . ')(' . $v['counts'] . ')</div>';
                }else{
                    $arr['name'] = '<div style="color:green">'.$v['inventory_class_name'] . '(' . $v['specification_type'] . ')(' . $v['counts'] . ')</div>';
                }
                $arr['open'] = false;
                if (!empty($v['children'])) {
                    $arr['children'] = $this->arrs($v['children'],$number);
                }
                $this->arr_s[] = $arr;
            }
        }
        return $this->arr_s;
    }

    /**
     * 部件
     * @return mixed
     */
    public function qrcodeRecord(){
        $id = input('id');
        $number = input('number');
        $product = db('products')->where('id',$id)->find();
        $arr = db('qrcode_record')
            ->alias('a')
            ->join('tf_process b', 'a.process_flow_id = b.id')
            ->where('a.base_code',$product['inventory_code'])
            ->where('a.pid',0)
            ->where('a.specification_type',$product['specification_type'])
            ->select();
        $a = count($arr);
        $num = $number-$a;
        if($num>0){
            $list = $this->getRecord($id,$arr,$num);
        }else{
            $list = $arr;
        }
        return json_encode($list);
    }

    /**
     * @return array
     */
    public function getRecord($id,$arr,$num)
    {
        $product = db('products')->where('pid',$id)->select();
        if(count($product)==0){
            $product = db('products')->where('id',$id)->find();
            $arrs[0]['id'] = $product['id'];
            $arrs[0]['inventory_class_name'] = $product['product_name'];
            $arrs[0]['specification_type'] = $product['specification_type'];
            $arrs[0]['counts'] =$num;
            $arrs[0]['pid'] =0;
            $arrs[0]['base_code'] = $product['inventory_code'];
            $arrs[0]['process_name'] = '未入库';
            $arr = array_merge($arr,$arrs);
        }else{
            foreach ($product as $v){
                $arrs = db('qrcode_record')
                    ->alias('a')
                    ->field('*,count(base_code) as counts')
                    ->join('tf_process b', 'a.process_flow_id = b.id')
                    ->where('a.base_code',$v['inventory_code'])
                    ->where('a.pid',0)
                    ->where('a.specification_type',$v['specification_type'])
                    ->select();
                if(is_array($arrs)){
                    $arrs[0]['id'] = $v['id'];
                    $arrs[0]['inventory_class_name'] = $v['product_name'];
                    $arrs[0]['specification_type'] = $v['specification_type'];
                    $arrs[0]['counts'] =$num;
                    $arrs[0]['pid'] =0;
                    $arrs[0]['base_code'] = $v['inventory_code'];
                    $arrs[0]['process_name'] = '未入库';
                    $arr = array_merge($arr,$arrs);
                }else{
                    $arr = array_merge($arr,$arrs);
                    $a = count($arrs);
                    $num = $num-$a;
                    if($num>0){
                        $arr = $this->getRecord($v['id'],$arr,$num);
                    }
                }
            }
        }

        return $arr;
    }

    public function isQrcodeRecord($id,$number,$process_flow_id=0){
        $product_list = db('products')->where('pid',$id)->select();
        //print_r($product_list); die();
        $data = array();
        foreach ($product_list as $k=>$v){
            //print_r($v);
            $where=array();
            if(!empty($process_flow_id)){
                $where['process_flow_id'] = $process_flow_id;
            }
            $find = db('qrcode_record')
                ->distinct('base_code')
                ->field('*,count(base_code) as counts')
                ->where('base_code',$v['inventory_code'])
                ->where('pid','0')
                ->where($where)
                ->group('base_code')->find();
            //print_r($find);die;
            if(!empty($find)){
               $find['id'] = $v['id'];
               $find['pid'] = $v['pid'];
               $product_lists = db('products')->where('pid',$v['id'])->find();
               if(!empty($product_lists)){
                   $find['children'] = $this->isQrcodeRecord($v['id'],$number);
               }
            }else{
                $product_list[$k]['base_code'] = $product_list[$k]['inventory_code'];
                $product_list[$k]['inventory_class_name'] = $product_list[$k]['product_name'];
                $find = $product_list[$k];
                $find['counts'] = 0;
                $product_lists = db('products')->where('pid',$v['id'])->find();
                if(!empty($product_lists)){
                    $find['children'] = $this->isQrcodeRecord($v['id'],$number);
                }
            }
            $data[$k] = $find;
        }
        return  $data;
    }
}