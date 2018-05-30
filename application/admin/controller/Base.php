<?php
namespace app\admin\controller;
use think\Controller;
class Base extends Controller
{
	Public function __construct(){
		parent::__construct();
		self::check_admin();
		self::getManagerName();//获取操作项
		$uploader = new \Uploader\Uploader;
        $this->assign('uploader', $uploader);

	}
	//验证登录
    final public function check_admin() {
		$module=strcasecmp(request()->module(),'admin');
		$controller=strcasecmp(request()->controller(),'index');
    	if($module=='0' && $controller == '0' && in_array(request()->action(), array('login','verify','logout'))) {
			return true;
		} else {
			//验证管理员
			$admin_uid = session('admin_uid');
			if(!$admin_uid){
				$loginUrl = url('admin/index/login');
				echo '<script>window.top.location.href = "'.$loginUrl.'";</script>';
			}
			if($admin_uid != 1){
				$this->checkRule();
			}
		}
	}
	final public function checkRule(){
		$where['m']=request()->module();
		$where['c']=request()->controller();
		$where['a']=request()->action();
		$authRuleDb = db('Admin_menu');
		if($menuId=$authRuleDb->where($where)->value('id')){
			$whereAdmin['uid'] =session('admin_uid');
        	$group_id = db('Auth_group_access')->where($whereAdmin)->value("group_id");
			$whereGroup['id'] = $group_id;
			$whereGroup['menu_ids'] = array('like','%'.$menuId.',%');
			$res= db('Auth_group')->where($whereGroup)->find();
			if(!$res){
				echo '<script>alert("没有权限访问");</script>';
				die;
			}
		}
		return true;
	}
	//获取左侧菜单栏
    final public function getMenus(){
    	$menuDb = db('Admin_menu');
		$groupDb = db('Auth_group');
		$groupAccessDb = db('Auth_group_access');
		$admin_uid = session('admin_uid');
		if($admin_uid){
			// 获取用户组
			$whereAdmin['uid'] = $admin_uid;
			$group_id = $groupAccessDb->where($whereAdmin)->value("group_id");
			$whereGroup['id'] = $group_id;
			$menu_ids = $groupDb->where($whereGroup)->value("menu_ids");
			$tree = new \Tree\Tree;
			$whereMenu['display'] = 1;
			if($admin_uid != 1){
				$whereMenu['id'] = array('IN',$menu_ids);
			}
			$data = $menuDb->where($whereMenu)->order('sort asc,id asc')->select();
			$menuList = $tree->makeTree($data);
			return $menuList;
		}
	}
	//获取操作项
    final public function getManagerName(){
		$whereP['m'] = request()->module();
		$whereP['c'] = request()->controller();
		$whereP['a'] = request()->action();
		$whereP['type'] = 1;
		$whereP['name']=input('menuName'); //菜单名称条件 M.R 添加
		$menuPid = db('Admin_menu')->where($whereP)->value('id');
		$whereTop['parent_id'] = $whereTable['parent_id'] = $menuPid;
		$whereTop['type'] = $whereTable['type'] = 2;
		$whereTop['display'] = $whereTable['display'] = 1;
		$whereTop['pos'] = 1;
		$whereTable['pos'] = 2;
		$topBtns = db('Admin_menu')->where($whereTop)->order('sort asc')->select();
		$tableBtns = db('Admin_menu')->where($whereTable)->order('sort asc')->select();
		$this->assign("topBtns",$topBtns);
		if($tableBtns){
			$tableBtns = json_encode($tableBtns);
			$this->assign("tableBtns",$tableBtns);
		}else{
			$this->assign("tableBtns",null);
		}
	}
}
