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
        $data = $this->where("id",$id)->find();
        return $data;
    }


}