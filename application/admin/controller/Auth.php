<?php
namespace app\admin\controller;
class Auth extends Base {
    public function __construct(){
        parent::__construct();
        $this->ruleDb = db('Auth_rule');
    }
    public function ruleLists(){
        if(request()->isPost()){
            $count = $this->ruleDb->count();
            $pageNo = input('page') ? input('page') : 1;
            $pageSize = input('rp') ? input('rp') : 1;
            $start = ($pageNo-1)*input('rp');
            $limit = $start.','.input('rp');
            $data = $this->ruleDb->limit($limit)->order('sort asc')->select();
            $returnData['total'] = $count;
            $returnData['pages'] = ceil($count / $pageSize);
            $returnData['page'] = $pageNo;
            $returnData['rows'] = $data;
            return json($returnData);
        }else{
            return $this->fetch("rule_list");
        }
    }
    /**
    * 规则增加
    */
    public function ruleAdd(){
        if(request()->isPost()){
            $data = input('info/a');
            $result = $this->ruleDb->insert($data);
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            return $this->fetch("rule_add");
        }
    }

    /**
    * 规则编辑
    */
    public function ruleEdit(){
        if(request()->isPost()){
            $where['id'] = input("id");
            $data = input('info/a');
            $this->ruleDb->where($where)->update($data);
            $this->success("操作成功");
        }else{
            $where['id']  = input("id");
            $data = $this->ruleDb->where($where)->find();
            $this->assign($data);
            return $this->fetch('rule_edit');
        }

    }
    /**
    *规则删除
    */
    public function ruleDelete(){
        if(request()->isPost()){
            $id = input('id');
            $whereData['parent_id'] = $id;
            $ruleData = $this->ruleDb->where($whereData)->find();
            if($ruleData){
                $this->error("请先删除子规则");
            }else{
                $where['id'] = $id;
                $this->ruleDb->where($where)->delete();
                $this->success("删除成功");
            }
        }
    }
}