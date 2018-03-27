<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/3/23
 * Time: 13:58
 */

namespace app\admin\model;


use think\exception\PDOException;
use think\Model;
use think\Db;

class AdminGroup extends Model
{
    protected $table='fengning_admin_group';
    //自动写入创建、更新时间
    protected $autoWriteTimestamp = true;

    public function getNodeInfo()
    {
        $result = $this->field('id,pid,name,category')->select();
        $str = "";
        foreach($result as $key=>$vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['name'].'"'.',"category":"'.$vo['category'].'"';
            $str .= '},';
        }
        return "[" . substr($str, 0, -1) . "]";
    }

    /*
     * 组织机构下的人员管理
     */

    public function getNodeName()
    {
        $result = $this->field('id,pid,name')->select();
        $str = "";
        foreach($result as $key=>$vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pid":"' . $vo['pid'] . '", "name":"' . $vo['name'].'"';
            $str .= '},';
        }
        $user = Db::name('admin')->field('id,admin_group_id,name')->select();
        foreach($user as $key=>$vo){
            $id = $vo['id'] + $vo['admin_group_id'] + 10000;
            $str .= '{ "id": "' . $id . '", "pid":"' . $vo['admin_group_id'] . '", "name":"' . $vo['name'].'"';
            $str .= '},';
        }
        return "[" . substr($str, 0, -1) . "]";
    }

    public function isParent($id)
    {
        $is_exist = $this->where('pid',$id)->find();
        return $is_exist;
    }


    public function insertTb($param)
    {
        try{
            $result = $this->allowField(true)->save($param);
            if(false === $result){
                return ['code' => -1,'msg' => $this->getError()];
            }else{
                return ['code' => 1,'msg' => '添加成功'];
            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    public function editTb($param)
    {
        try{
            $result = $this->allowField(true)->save($param,['id' => $param['id']]);
            if(false === $result){
                return ['code' => -1,'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'msg' => '编辑成功'];
            }
        }catch(PDOException $e){
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function deleteTb($id)
    {
        try{
            $this->where('id',$id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        }catch(PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    public function getOne($id)
    {
        $data = $this->find($id);
        return $data;
    }

    /*
 * 查询所有的角色类型表中的数据
 */

    public function getall()
    {
        return $this->field("id,pid,name")->select();
    }
}