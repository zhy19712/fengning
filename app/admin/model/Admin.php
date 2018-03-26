<?php

namespace app\admin\model;

use think\Db;
use \think\Model;
class Admin extends Model
{
	public function admincate()
    {
        //关联角色表
        return $this->belongsTo('AdminCate');
    }


    public function log()
    {
        //关联日志表
        return $this->hasOne('AdminLog');
    }

    /**
     * 根据组织机构 编号 关联删除 用户
     * @param $group_id
     * @return array
     * @author hutao
     */
    public function delUserByGroupId($group_id)
    {
        $user = $this->where('admin_group_id',$group_id)->column('id','thumb');
        if(count($user) > 0){
            $thumbArr = array_values($user);
            $thumbPath = Db::name('attachment')->whereIn($thumbArr)->column('filepath');
            if(count($thumbPath) > 0){
                // 删除每一个用户的头像
                foreach($thumbPath as $k=>$v){
                    if(file_exists($v)){
                        unlink($v); //删除文件
                    }
                }
            }
            // 删除用户记录
            $idArr = array_keys($user);
            $this->delete($idArr);
        }
        return ['code' => 1, 'msg' => '删除成功'];
    }

}
