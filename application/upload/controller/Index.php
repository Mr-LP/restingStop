<?php
namespace app\upload\controller;

use think\Controller;
class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    // 文件上传
    public function upload()
    {
        $file = request()->file();
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file && count($file)==1){
            $size=1024*10*10*10*4;//4M  1024 B 开始
            foreach ($file as $v) {
                $info = $v->validate(['size'=>$size,'ext'=>'jpg,png,gif,jpeg,bmp'])->move(Config('UPLOAD_PATH').Config('FOLDER'));
                if ($info) {
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                    $data['status'] = "success";
                    $data['file']=Config('UPLOAD_PATH');
                    $data['path']='/'.Config('FOLDER').$info->getSaveName();
                } else {
                    // 上传失败获取错误信息
                    $data['status']='error';
                    $data['info']=$v->getError();
                }
            }
        }else{
            $data['status']='error';
            $data['info']="请上传一个文件";
        }
        return json($data);
    }
    public function index(){
        echo '<form action="'.url('upload').'" enctype="multipart/form-data" method="post" >
        <input type="file" name="img">
        <input type="file" name="ppp">
        <input type="submit" value="提交">
        </form>';
    }
}
