<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/5
 * Time: 17:31
 */
/*
 * 档案管理-分支目录管理-项目分类
 * @package app\filemanagement\model
 */
namespace app\filemanagement\model;
use think\exception\PDOException;
use \think\Model;

class Filependingdocuments extends Model
{
    protected $name='file_pending_documents';

    /**
     * 新增整理文件
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
                return ['code' => 1,'msg' => '添加成功'];
            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }
}