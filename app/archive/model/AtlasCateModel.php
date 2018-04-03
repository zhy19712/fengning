<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 14:12
 */
/*
 * 图纸文档管理，图册文件
 * @package app\archive\controller
 */
namespace app\archive\model;

use think\Db;
use \think\Model;

class AtlasCateModel extends Model
{
    protected $name='atlas_cate';

    /**
     * 新增一条图册记录
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
    /**
     * 查询同一类别，同一selfid下的最大序号cate_number
     */
    public function maxcatenumber($selfid)
    {
        $catenumber = $this->where("selfid",$selfid)->max("cate_number");
        return $catenumber;
    }
    /**
     * 编辑一条图册记录
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
     * 根据图册id删除一条图册记录
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
    /**
    * 获取一条图册类型信息
    */
    public function getOne($id)
    {
        $data = $this->where('id', $id)->find();
        return $data;
    }

    /**
     * 根据id查询该id下是否存在子类
     */
    public function judge($id)
    {
        $data = $this->where('pid', $id)->find();
        return $data;
    }
    /**
     * 查询一条图册下的所有的图片信息
     */
    public function getAllpicture($id)
    {
        //定义一个空的数组
        $children = array();

        $data = $this
            ->field('picture_number,picture_name,picture_papaer_num,date,paper_category,owner,completion_date,id,pid')
            ->where('pid', $id)
            ->select();
        if($data)
        {
            foreach ($data as $k=>$v)
            {
                $children[$k][] = '';
                $children[$k][] = $v['picture_number'];
                $children[$k][] = $v['picture_name'];
                $children[$k][] = $v['picture_papaer_num'];
                $children[$k][] = '';
                $children[$k][] = '';
                $children[$k][] = '';
                $children[$k][] = '';
                $children[$k][] = $v['completion_date'];
                $children[$k][] = '';
                $children[$k][] = $v['paper_category'];
                $children[$k][] = $v['owner'];
                $children[$k][] = $v['date'];
                $children[$k][] = $v['id'];
                $children[$k][] = $v['pid'];
            }
        }


        return $children;
    }

    /**
     * 根据节点id查询图册表的信息
     *
     */
    public function getpicinfo($id)
    {
        $data = $this->field("path")->where("selfid",$id)->select();
        return $data;
    }

    /*
     * 根据图册id删除一条图册记录
     */
    public function delselfidCate($id)
    {
        try{
            $this->where("selfid",$id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        }catch(PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /*
     * 查询该图册下的所有的文件路径
     */
    public function  getallpath($id)
    {
        $data = $this->field("path")->where("pid",$id)->select();

        return $data;
    }

    /**
     * 查询当前图册下的被禁用的黑名单
     */

    public function getbalcklist($id)
    {
        $data = $this->field("blacklist")->where("id",$id)->find();
        return $data;
    }

    /**
     * 根据传过来的fengning_atlas_cate表中的id,admin表中的id,
     */

    public function deladmincateid($param)
    {
        //admin_id是admin表id，id为cate表id

        $admin_cate_id = $this->field("admin_cate_id")->where("id",$param['admin_id'])->find();



        if($admin_cate_id["admin_cate_id"])
        {
            $cate_id = explode(",",$admin_cate_id["admin_cate_id"]);

            foreach($cate_id as $k=>$v)
            {
                if($v == $param['id'])
                {
                    unset($cate_id[$k]);
                }
            }
        }
        if($cate_id)
        {
            $str = implode(",",$cate_id);

        }

        //把处理过得数据重新插入数组中
        $result = $this->allowField(true)->update(['admin_cate_id'=>$str],['id' => $param['admin_id']]);

        if($result)
        {
            return ['code' => 1,'msg' => "删除成功"];
        }else{
            return ['code' => -1,'msg' => "删除失败"];
        }

    }



}