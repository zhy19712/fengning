<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/18
 * Time: 17:30
 */

namespace app\quality\model;


use think\Db;
use think\exception\PDOException;
use think\Model;

class PictureModel extends Model
{
    protected $name = 'quality_model_picture';
    //自动写入创建、更新时间 insertGetId和update方法中无效，只能用于save方法
    protected $autoWriteTimestamp = true;

    public function insertTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'msg' => '关联成功'];
            }
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function editTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'msg' => '编辑成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function getAllName($id,$search_name='')
    {
        // type 和 picture_type  1工程划分模型 2 建筑模型 3三D模型
        // 用于初始化时 选中(回显) 之前关联过的模型图的主键
        $picture_id = Db::name('quality_model_picture_relation')->where(['type'=>1,'relevance_id'=>$id])->value('picture_id');
        // 全部的列表
        if(empty($search_name)){
            $data = $this->where('picture_type',1)->column('id as picture_id,picture_number,picture_name');
        }else{
            $data = $this->where(['picture_type'=>1,'picture_name'=>['like','%'.$search_name.'%']])->column('id as picture_id,picture_number,picture_name');
        }
        $str = '';
        foreach ($data as $v) {
            $str .= '{ "id": "' . $v['picture_id'] . '", "pId":"' . 0 . '", "name":"' . $v['picture_name'] . '"' . ',"picture_number":"'.$v['picture_number'].'"' . ',"picture_id":"'.$v['picture_id'].'"';
            $str .= '},';
        }
        $data['one_picture_id'] =  $picture_id;
        $data['str'] =  "[" . substr($str, 0, -1) . "]";
        return $data;
    }

    public function getRemarkTb($id)
    {
        $remark = $this->where(['id'=>$id])->value('remark');
        return $remark;
    }

}