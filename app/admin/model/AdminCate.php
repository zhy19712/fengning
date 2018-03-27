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
        $data = $this->where("pid",$id)->column("id");
        return $data;
    }
    /*
     * 新增角色类型
     */
    public function insertCate($param)
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
    /*
     * 编辑角色类型
     */
    public function editCate($param)
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
    /*
     * 删除角色类型
     */
    public function delCate($id)
    {
        try{
            $this->where("id",$id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        }catch(PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }
    /*
     * 获取一条角色类型信息
     */
    public function getOne($id)
    {
        $data = $this->where("id",$id)->find();
        return $data;
    }

    /*
     * 根据admin_cate用户表中的admin_id字段查询admin表中对应的用户
     */

    public function getAdminid($id)
    {
        $data = $this->field("admin_id")->where('id',$id)->find();
        return $data;
    }

    /*
     * 根据传过来的admin_cate表中的id删除admin_cate用户表中的admin_id
     */

    public function delAdminid($param)
    {
        $admin_id = $this->field("admin_id")->where("id",$param['id'])->find();
        if($admin_id)
        {
            $admin_id = explode(",",$admin_id['admin_id']);
            foreach ($admin_id as $k=>$v)
            {
                if($param['admin_id'] == $v)
                {
                    unset($admin_id[$k]);
                }
            }
            $admin_id = implode(",",$admin_id);

            //把处理过得数据重新插入数组中
            $result = $this->allowField(true)->save(['admin_id'=>$admin_id],['id' => $param['id']]);
            if($result)
            {
                return ['code' => 1,'msg' => "删除成功"];
            }

        }else{
            return ['code' => -1,'msg' => "删除失败"];
        }
    }

    /*
     * 根据传过来的admin_cate表中的id,admin表中的id，添加到admin_cate用户表中的admin_id
     */

    public function addAdminid($param)
    {
        $admin_id = $this->field("admin_id")->where("id",$param['id'])->find();
        if($admin_id)
        {
            $admin_id = explode(",",$admin_id['admin_id']);

            array_push($admin_id,strval($param['admin_id']));

            $admin_id = implode(",",$admin_id);



            //把处理过得数据重新插入数组中
            $result = $this->allowField(true)->save(['admin_id'=>$admin_id],['id' => $param['id']]);
            if($result)
            {
                return ['code' => 1,'msg' => "添加成功"];
            }

        }else{
            return ['code' => -1,'msg' => "添加失败"];
        }
    }




}
