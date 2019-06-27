<?php
namespace app\admin\controller;

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
//        $number = $_POST['number'];
        $where = $specification_type.'%';
        /**
         * 查出所有有关电机型号的所有部件总装，以及其他工序
         * 根据数据判断各个部件的数量是否齐套
         * 如果不齐套 显示为红色并显示每个部件所在位置
         * 不够测用未出库（入库的）未入库（没有入库记录0）
         */
//        $data = db('qrcode_record')
//            ->field('*,count(base_code) as counts')
//            ->where('specification_type','like',$where)
//            ->where(['process_flow_id'=>18,])
//            ->where('pid',0)
//            ->group('base_code')->select();

        $data = db('qrcode_record')
            ->field('*,count(base_code) as counts')
            ->where('specification_type','like',$where)
            ->where('process_flow_id','<>',18)
            ->where('pid',0)
            ->group('base_code')->select();
        print_r($data);die;
        $arr =  array();
        return $this->fetch('matching_post');
    }

}