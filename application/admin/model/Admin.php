<?php
namespace app\admin\model;

use think\Model;
class Admin extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp  = TRUE;
    public function access()
    {
        return $this->hasOne('authGroupAccess','uid')->field('group_id');
    }
}
