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

    public function getNodeInfo($type = 'group')
    {
        if($type == 'group'){
            $result = $this->field('id,pid,name,category,idv,pidv')->select();
            $str = "";
            foreach($result as $key=>$vo){
                $str .= '{ "id": "' . $vo['idv'] . '", "pId":"' . $vo['pidv'] . '", "name":"' . $vo['name'].'"'.',"category":"'.$vo['category'].'"'.',"idv":"'.$vo['id'].'"'.',"pidv":"'.$vo['pid'].'"';
                $str .= '},';
            }
        }else{
            $result = Db::name('admin_cate')->field('id,pid,role_name')->select();
            $str = "";
            foreach($result as $key=>$vo){
                $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['role_name'].'"';
                $str .= '},';
            }
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
            $id = $this->allowField(true)->insertGetId($param);
            $node = $this->getOne($id);
            $result = $this->where('id',$id)->update(['idv' => $id,'pidv' => $node['pid']]);
            if(1 == $result){
                return ['code' => 1,'msg' => '添加成功'];
            }else{
                return ['code' => -1,'msg' => $this->getError()];
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
     * 查询所有组织机构表中的所有数据
     */

    public function getall()
    {
        return $this->field("id,pid,name")->select();
    }
}