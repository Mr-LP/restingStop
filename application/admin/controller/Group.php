<?php
namespace app\admin\controller;

class Group extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->groupDb = Db("Auth_group");
        $this->groupAccessDb = Db("Auth_group_access");
        $this->ruleDb = Db('Auth_rule');
        $this->menuDb = Db('Admin_menu');
        $whereData['status'] = 1;
        $groupInfo= $this->groupDb->where($whereData)->select();
        $this->assign("groupInfo", $groupInfo);
    }
    /**
    * 管理组列表
    */
    public function groupLists()
    {
        if (request()->isPost()) {
            $count = $this->groupDb->count();
            $pageNo = input('page') ? input('page') : 1;
            $pageSize = input('rp') ? input('rp') : 20;
            $start = ($pageNo-1)*input('rp');
            $limit = $start.','.input('rp');
            $dataLists = $this->groupDb->limit($limit)->where($where)->select();
            $returnData['total'] = $count;
            $returnData['pages'] = ceil($count/$pageSize);
            $returnData['page'] = input('page')?input('page'):1;
            $returnData['rows'] = $dataLists;
            return json($returnData);
        } else {
            return view('list');
        }
    }
    /**
    * 管理组增加
    */
    public function groupAdd()
    {
        if (request()->isPost()) {
            $data = input('info/a');
            $data['module']='admin';
            if (!$data['title']) {
                $this->error("请输入管理组名称");
            } else {
                $data['type'] = 1;
                $result = $this->groupDb->insert($data);
                if ($result) {
                    $this->success("操作成功");
                } else {
                    $this->error("操作失败");
                }
            }
        } else {
            return view("add");
        }
    }

    /**
    * 管理组编辑
    */
    public function groupEdit()
    {
        if (request()->isPost()) {
            $id = $where['id'] = input('id');
            $data = input('info/a');
            $result = $this->groupDb->where($where)->update($data);
            $this->success("操作成功");
        } else {
            $where['id'] = input('id');
            $groupInfo = $this->groupDb->where($where)->find();
            $this->assign($groupInfo);
            return view('edit');
        }
    }
    /**
    *管理组删除
    */
    public function groupDelete()
    {
        if (request()->isPost()) {
            $id = intval(input('id'));
            if ($id == '1') {
                $this->error("当前管理组不允许删除");
            }
            $whereData['id'] = $id;
            // 判断当前管理组是否有会员
            $whereGroupAccess['group_id'] = $id;
            $groupAccessData = $this->groupAccessDb->where($whereGroupAccess)->find();
            if ($groupAccessData) {
                $this->error('当前管理组存在管理员');
            }
            $this->groupDb->where($whereData)->delete();
            $this->success('删除成功');
        }
    }
    // 管理组设置规则
    public function groupSettingRule()
    {
        if (IS_POST) {
            $whereGroup['id'] = $id = I("groupId");
            $ids = I('ids');
            if ($ids) {
                $this->groupDb->where($whereGroup)->setField("rules", $ids);
                $this->success("操作成功");
            } else {
                $this->error("操作失败");
            }
        } else {
            $whereGroup['id'] = $id = I("groupId");
            $groupData = $this->groupDb->where($whereGroup)->find();
            $groupData['rules']  =  explode(',', $groupData['rules']);
            $this->assign($groupData);
            // 获取
            $tree = new \Org\Tree\Tree;
            $data = $this->ruleDb->order('sort desc,id desc')->select();
            $ruleList = $tree->makeTree($data);
            $this->assign("ruleList", $ruleList);
            // layout(false);
            $this->display("group_rule_list");
        }
    }
    // 管理组设置菜单
    public function groupSettingMenu()
    {
        if (request()->isPost()) {
            $whereGroup['id'] = input("groupId");
            $ids = input('ids');
            if ($ids) {
                $d=$this->groupDb->where($whereGroup)->setField("menu_ids", $ids);
                $this->success('操作成功');
            } else {
                $this->error("操作失败");
            }
        } else {
            $whereGroup['id'] = input("id");
            $groupData = $this->groupDb->where($whereGroup)->find();
            $groupData['menu_ids']  =  explode(',', $groupData['menu_ids']);
            $this->assign($groupData);
            // 获取
            $whereDbenu['display'] = 1;
            $tree = new \Tree\Tree;
            $data = $this->menuDb->order('sort desc,id desc')->where($whereDbenu)->select();
            $menuList = $tree->makeTree($data);
            $this->assign("menuList", $menuList);
            return view("menu_list");
        }
    }
}
