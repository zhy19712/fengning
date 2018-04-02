<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/3/23
 * Time: 13:58
 */

namespace app\quality\model;


use think\Db;
use think\exception\PDOException;
use think\Model;

class DivisionModel extends Model
{
    protected $name = 'quality_division';
    //自动写入创建、更新时间 insertGetId和update方法中无效，只能用于save方法
    protected $autoWriteTimestamp = true;

    public function getNodeInfo()
    {
        $section = Db::name('section')->column('id,code,name'); // 标段列表
        $division = $this->column('id,pid,d_name,section_id,type'); // 工程列表
        $num = $this->count() + 1000;

        $str = "";
        $str .= '{ "id": "' . 1 . '", "pId":"' . 0 . '", "name":"' . '丰宁抽水蓄能电站' .'"';
        $str .= '},';
        foreach($section as $v){
            $pid =$v['id'] + $num;
            $str .= '{ "id": "' . $pid . '", "pId":"' . 1 . '", "name":"' . $v['name'].'"' . ',"code":"' . $v['code'] .'"';
            $str .= '},';
            // 分类 1单位 2分部 3分项
            foreach($division as $vo){
                if($v['id'] == $vo['section_id']){
                    if($vo['type'] == 1){
                        $str .= '{ "tid": "' . $vo['id'] . '", "pId":"' . $pid . '", "name":"' . $vo['d_name'].'"';
                        $str .= '},';
                    }else{
                        $str .= '{ "tid": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['d_name'].'"';
                        $str .= '},';
                    }
                }
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
            $result = $this->allowField(true)->save($param);
            $id = $this->getLastInsID();
            $data = $this->getOne($id);
            if(false === $result){
                return ['code' => -1,'msg' => $this->getError()];
            }else{
                return ['code' => 1,'data' => $data,'msg' => '添加成功'];
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

}