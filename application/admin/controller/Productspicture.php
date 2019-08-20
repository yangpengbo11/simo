<?php


namespace app\admin\controller;
use think\Request;

class Productspicture extends Base
{
    public function index(){
      return $this->fetch();
    }

    public function productspicture_post(){
        $qrcode_content=$_POST['qrcode_content'];
        $files= Request::instance()->file('imgs'); //得到传输的数据,以数组的形式
        if(count($files)==0){
            return $this->alert('你没有选择图片,请选择图片','/admin/Productspicture/index',5);
        }
        $i=1;
        $erweima=$this->isexistence($qrcode_content);
        if($erweima && $erweima['types']==3){
            foreach($files as $file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $filename=date('YmdHi').$i;
                $path=ROOT_PATH .'public' . DS . 'uploads'.DS.date('Ymd');
                $info = $file->move($path,$filename);
                $i++;
                $url =  'uploads'.DS.date('Ymd'). DS .$info -> getSaveName();
                $data=['qrcode_content'=>$qrcode_content,
                    'picture_url'=>$url,
                    'operator'=>session('users')['account_name'],
                    'create_time'=>date('Y-m-d H:i:s')
                ];
                db('products_picture')->insert($data);
            }
            return $this->alert('上传成功','/admin/Productspicture/index',6);
        }else{
            return $this->alert('你输入的二维码错误或者不是成品二维码','/admin/Productspicture/index',5);
        }


    }

    public function select(){
       return $this->fetch('select');
    }

    public function find(){
        $qrcode_content=$_POST['qrcode_content'];
        $res=db('products_picture')->where(['qrcode_content'=>$qrcode_content])->select();
        if($res){
            $this->assign('data',$res);
            $this->assign('qrcode',$qrcode_content);
            return $this->fetch('find');
        }else{
           return  $this->alert('你输入的成品码错误或者没有上传图片','/admin/Productspicture/select',5);
        }

    }

}