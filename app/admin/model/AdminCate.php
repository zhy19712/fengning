<?php

namespace app\admin\model;

use \think\Model;
class AdminCate extends Model
{
	public function admin()
    {
        //关联管理员表
        return $this->hasOne('Admin');
    }
    /*
     * 根据admin_cate_type中的id查询admin_cate表中的所有的pid对应的id
     */
    public function findcateid($id)
    {
        $data = $this->field("id")->where("pid",$id)->select();
        return $data;
    }
}
