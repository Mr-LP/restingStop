<?php
namespace app\admin\controller;

class Index extends Base
{
    Public function __construct(){
        parent::__construct();
        $this->adminDb=db('Admin');
    }
    public function index(){
        $data=$this->getMenus();
        $this->assign('menuList',$data);
        return $this->fetch('index');
    }
    public function main(){
        return $this->fetch('main');
    }
    public function login(){
        if(cookie('admin_uid') && session('admin_uid')){
            $this->success('登录成功','index','','1');
        }
        if(request()->isPost()){
            $validate=new \think\Validate([
                'captcha'   => 'require|captcha',
                'username'  => 'require|min:2',
                'password'  => 'require|min:5',
                '__token__' => 'require|token'
            ]);
            $data['username']=input('username');
            $data['password']=input('password');
            $data['__token__']=input('__token__');
            $data['captcha']=input('captcha');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $where['username'] = $username =$data['username'];
            //根据用户名获取用户信息
            $adminInfo = $this->adminDb->where($where)->find();
            if(!$adminInfo){
                $this->error('管理员不存在');
            }else{
                if($adminInfo['status'] == -1){
                    $this->error('禁止登录');
                }
                $password = password($data['password'],$adminInfo['encrypt']);
                if($password==$adminInfo['password']){
                    //验证成功
                    cookie('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_username',''.$adminInfo['username'].'');
                    session('admin_realname',''.$adminInfo['realname'].'');
                    session('admin_avatar',''.$adminInfo['avatar'].'');
                    session('login_last_date',''.time().'');
                    // 更新登录时间
                    $updateData['uid'] = $adminInfo['uid'];
                    $updateData['login_time'] = date("Y-m-d H:i:s",time());
                    $updateData['login_ip'] = request()->ip();
                    $this->adminDb->update($updateData);
                    $this->success('登录成功','index');
                }else{
                    $this->error('密码错误');
                }
            }
        }else{
            return $this->fetch('login');
        }
    }
    public function logout(){
        cookie('admin_uid',NULL);
        session('admin_uid',NULL);
        session('admin_username',NULL);
        session('admin_realname',NULL);
        session('admin_avatar',NULL);
        $this->redirect('index');
    }
}
