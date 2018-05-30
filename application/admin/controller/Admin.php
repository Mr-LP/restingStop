<?php
namespace app\admin\controller;

class Admin extends Base
{
    Public function __construct(){
        parent::__construct();
        $this->adminDb=model('Admin');
        $this->groupDb = db("Auth_group");
        $this->groupAccessDb = model("Auth_group_access");
        //全部用户组获取
        $groupInfo= $this->groupDb->select();
        $this->assign("groupInfo",$groupInfo);
    }
    public function adminList(){
        if(request()->isPost()){
            if(session('admin_uid') != "1"){
                $where['uid'] = array('neq',1);
            }
            // foreach ($this->groupInfo as $k => $v) {
            //     $groupId[]=$v['id'];
            // }
            // $where['group_id']=array('in',$groupId);
            if(input('group_id')){
                $where['group_id']=input('group_id');
            }
            if(input('realname')){
                $where['realname']=array('like','%'.input('realname').'%');
            }
            $count = $this->adminDb->where($where)->count();
            $pageNo = input('page') ? input('page') : 1;
            $pageSize = input('rp') ? input('rp') : 20;
            $start = ($pageNo-1)*input('rp');
            $limit = $start.','.input('rp');
            $dataLists = $this->adminDb->where($where)->limit($limit)->order('uid desc')->select();
            $returnData['total'] = $count;
            $returnData['pages'] = ceil($count/$pageSize);
            $returnData['page'] = input('page')?input('page'):1;
            $returnData['rows'] = $dataLists;
            return json($returnData);
        }else{
            return $this->fetch('list');
        }
    }
    Public function adminAdd(){
        if(request()->isPost()){
            $data=input('info/a');
            if($data['password']){
                $password = password($data['password']);
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
            }
            if(!$data['group_id']){
                $this->error('没有用户组');
                return false;
            }
            $where['username'] = $data['username'];
            $isIn = $this->adminDb->where($where)->find();
            if($isIn){
                $this->error('账号已存在');
                return false;
            }
            $data['addtime']=date('Y-m-d H:i:s',time());
            $data['add_ip']=request()->ip();
            $data['access']['group_id']=$data['group_id'];
            $res=$this->adminDb->together('access')->save($data);
            if($res){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            return view('add');
        }
    }
    public function adminEdit(){
        if(request()->isPost()){
            $data = input('info/a');
            $whereData['uid'] = $data['uid'];
            if($this->checkuserinfo($data)){
                $this->error("提交信息不合法");
            }
            if(input('password')){
                $password = password(input('password'));
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
            }
            $data['update_time'] = date("Y-m-d H:i:s");
            $data['access']['group_id'] = $data['group_id'];
            $result = $this->adminDb->find($data['uid'])->together('access')->save($data);
            // 如果为当前管理员更新本地缓存
            if($uid = session('admin_uid')){
                session('admin_username',''.$data['username'].'');
                session('admin_realname',''.$data['realname'].'');
                session('admin_avatar',''.$data['avatar'].'');
            }
            $this->success("操作成功");
        }else{
            if(input('id')){
                $whereData['uid'] = input("id");
            }else{
                $whereData['uid'] = session('admin_uid');
            }
            $adminInfo = $this->adminDb->where($whereData)->find()->toArray();
            $this->assign($adminInfo);
            return view("edit");
        }
    }
    public function adminDelete(){
        if(request()->isPost()){
            $res=$this->adminDb->where('uid='.input('id'))->delete();
            if($res){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
    public function checkuserinfo($data){
        if($this->checkname($data['username'],$data['uid'])){
            return true;
        }
        if($this->checkmobile($data['mobile'],$data['uid'])){
            return true;
        }
        if($this->checkemail($data['email'],$data['uid'])){
            return true;
        }
        return false;
    }
    public function checkname($name,$uid){
        $where['uid']=array('not in',$uid);
        $where['username']=$name;
        $res=$this->adminDb->where($where)->find();
        if($res){
            return true;
        }else{
            return false;
        }
    }
    public function checkmobile($mobile,$uid){
        $where['uid']=array('not in',$uid);
        $where['mobile']=$mobile;
        $res=$this->adminDb->where($where)->find();
        if($res){
            return true;
        }else{
            return false;
        }
    }
    public function checkemail($email,$uid){
        $where['uid']=array('not in',$uid);
        $where['email']=$email;
        $res=$this->adminDb->where($where)->find();
        if($res){
            return true;
        }else{
            return false;
        }
    }
}
