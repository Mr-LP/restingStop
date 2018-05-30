<?php
namespace app\admin\controller;

class Menu extends Base
{
    Public function __construct(){
        parent::__construct();
        $this->menuDb=db('Admin_menu');
    }
    public function menulists(){
        if(request()->isPost()){
            if(input('sorts/a')) {
                foreach(input('sorts/a') as $id => $sort) {
                    $where['id'] = $id;
                    $data['sort'] = $sort;
                    $this->menuDb->where($where)->update($data);
                }
                $this->success("排序成功");die;
            }
            $data = $this->menuDb->order('sort !=0 desc,sort,id asc')->select();
            foreach ($data as $key => $v) {
                if($v['parent_id'] == 0){
                    // 判断有无子分类
                    $whereChildren['parent_id'] = $v['id'];
                    $isChild = $this->menuDb->where($whereChildren)->find();
                    if($isChild){
                        $data[$key]['isLeaf'] = false;
                    }else{
                        $data[$key]['isLeaf'] = true;
                    }
                    $data[$key]['level'] = 0;
                    $data[$key]['parent'] = 0;
                }else{
                    $data[$key]['level'] = 1;
                    $data[$key]['isLeaf'] = true;
                    $data[$key]['parent'] = $v['parent_id'];
                }
                $data[$key]['laoded'] = true;
                $data[$key]['expanded']  = true;
            }
            $tree = new \Tree\Tree;
            $dataLists = $tree->makeTreeForHtml($data);
            return json($dataLists);
        }else{
            return $this->fetch('list');
        }
    }
    public function menuAdd(){  
        if(request()->isPost()){
            $data = input('info/a');
            if(( !$data['m'] || !$data['c'] || !$data['a']) && $data['parent_id']){
                $where['id']=$data['parent_id'];
                $parentData=$this->menuDb->where($where)->find();
                $data['m']=$parentData['m'];
                $data['c']=$parentData['c'];
                $data['a']=$parentData['a'];
            }
            $result = $this->menuDb->insert($data);
            if($result){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $where['parent_id'] = 0;
            $data = $this->menuDb->where($where)->select();
            $this->assign("menuList",$data);
            return $this->fetch('add');
        }
    }
    public function menuEdit(){
        if(request()->isPost()){
            $id = $where['id'] = input("id");
            $data = input('info/a');
            if(( !$data['m'] || !$data['c'] || !$data['a']) && $data['parent_id']){
                $parentwhere['id']=$data['parent_id'];
                $parentData=$this->menuDb->where($parentwhere)->find();
                $data['m']=$parentData['m'];
                $data['c']=$parentData['c'];
                $data['a']=$parentData['a'];
            }
            $result=$this->menuDb->where($where)->update($data);
            if($result){
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $id = $where['id'] = input("id");
            $whereParent['parent_id'] = 0;
            $parentData = $this->menuDb->where($whereParent)->select();
            $this->assign("parentData",$parentData);
            $data = $this->menuDb->where($where)->find();
            $this->assign($data);
            return $this->fetch('edit');
        }
    }
    public function menuDelete(){
        if(request()->isPost()){
            $id = input('id');
            $whereData['parent_id'] = $id;
            $menuData = $this->menuDb->where($whereData)->find();
            if($menuData){
                $this->error("请先删除子菜单");
            }else{
                $where['id'] = $id;
                $this->menuDb->where($where)->delete();
                $this->success("删除成功");
            }
        }
    }
}
