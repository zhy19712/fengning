<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/11
 * Time: 17:05
 */
/*
 * 档案管理-档案管理-待整理文件
 * @package app\filemanagement\model
 */
namespace app\filemanagement\model;
use think\exception\PDOException;
use \think\Model;

class Filependingdocuments extends Model
{
    protected $name='file_arrangement_file';

    /**
     * 新增待整理案卷
     * @param $param
     * @return array
     */
    public function insertPd($param)
    {
        try{
            $result = $this->allowField(true)->save($param);
            if(false === $result){
                return ['code' => -1,'msg' => $this->getError()];
            }else{
                $id = $this->getLastInsID();
                return ['code' => 1,'id'=>$id,'msg' => '添加成功'];
            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑待整理案卷
     * @param $param
     * @return array
     */
    public function editPd($param)
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

    /**
     * 删除待整理案卷
     * @param $id
     * @return array
     */
    public function delPd($id)
    {
        try{
            $this->where("id",$id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        }catch(PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /**
     * 获取一条待整理案卷信息
     * @param $id
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        $data = $this->where("id",$id)->find();
        return $data;
    }
}