<?php

namespace app\admin\model;

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

}
