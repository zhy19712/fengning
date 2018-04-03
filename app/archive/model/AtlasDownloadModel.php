<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/2
 * Time: 14:20
 */
/*
 * 记录下载信息
 * @package app\archive\controller
 */
namespace app\archive\model;

use think\Db;
use \think\Model;

class AtlasDownloadModel extends Model
{
    protected $name='atlas_download_record';

    /**
     * 新增下载记录
     * @param $param
     * @return array
     */
    public function insertDownload($param)
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
     * 获取该条图册的所有的下载记录信息
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getall($id)
    {
        try{
            $data = $this->where("cate_id",$id)->select();
            return $data;
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }

    }
}