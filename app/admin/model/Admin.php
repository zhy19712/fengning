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

    /**
     * 指定In查询条件
     * @access public
     * @param mixed  $field     查询字段
     * @param mixed  $condition 查询条件
     * @param string $logic     查询逻辑 and or xor
     * @return $this
     */
    function whereIn($field, $condition, $logic = 'AND')
    {
        $this->parseWhereExp($logic, $field, 'in', $condition);
        return $this;
    }

    /**
     * 根据角色类型管理 编号 关联删除 用户
     * @return array
     */
    public function delUserByCateId($cate_id)
    {
        $user = $this->where('admin_cate_id',$cate_id)->column('id','thumb');
        if(count($user) > 0){
            $thumbArr = array_values($user);

            $thumbPath = Db::name('attachment')->where(['id'=>['in',$thumbArr]])->column('filepath');

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

            $this->where(['id'=>['in',$idArr]])->delete();
        }
        return ['code' => 1, 'msg' => '删除成功'];
    }

    /**
     * 查询一个用户信息的用户名name
     */

    public function getName($where)
    {
        $data = $this->field("id,name")->where("id = ".$where['id']."  and name like '%" .$where['name']. "%'")->find();
        return $data;
    }



}
