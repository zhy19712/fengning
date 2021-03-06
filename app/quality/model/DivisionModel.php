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

    /**
     * 标段
     * @return \think\model\relation\HasOne
     */
    public function Section()
    {
        return $this->hasOne("app\contract\model\SectionModel", 'id', 'section_id');
    }

    /**
     * 工程划分 type = 1 ，单位质量管理 type = 2 ，分部质量管理 type = 4 共用 树节点
     * @param int $type
     * @return string
     * @author hutao
     */
    public function getNodeInfo($type = 1)
    {
        $section = Db::name('section')->column('id,code,name'); // 标段列表
        $division = $this->column('id,pid,d_name,section_id,type,en_type,d_code'); // 工程列表
        $num = $this->count() + Db::name('section')->count() + 10000;

        $str = "";
        $open = 'true';
        $str .= '{ "id": "' . -1 . '", "pId":"' . 0 . '", "name":"' . '丰宁抽水蓄能电站' . '"' . ',"open":"' . $open . '"';
        $str .= '},';
        foreach ($section as $v) {
            $id = $v['id'] + $num;
            $str .= '{ "id": "' . $id . '", "pId":"' . -1 . '", "name":"' . $v['name'] . '"' . ',"code":"' . $v['code'] . '"' . ',"section_id":"' . $v['id'] . '"' . ',"add_id":"' . $v['id'] . '"';
            $str .= '},';
            // 单位工程 type = 1 子单位工程 type = 2 分部工程  type = 3 子分部工程 type = 4 分项工程   type = 5 单元工程   type = 6
            foreach ($division as $vo) {
                if ($v['id'] == $vo['section_id']) {
                    if ($vo['type'] == 1) {
                        $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $id . '", "name":"' . $vo['d_name'] . '"' . ',"d_code":"' . $vo['d_code'] . '"' . ',"section_id":"' . $vo['section_id'] . '"' . ',"add_id":"' . $vo['id'] . '"' . ',"edit_id":"' . $vo['id'] . '"' . ',"type":"' . $vo['type'] . '"' . ',"en_type":"' . $vo['en_type'] . '"';
                        $str .= '},';
                    } else {
                        if ($type == 2) {
                            if ($vo['type'] == $type) {
                                $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['d_name'] . '"' . ',"d_code":"' . $vo['d_code'] . '"' . ',"section_id":"' . $vo['section_id'] . '"' . ',"add_id":"' . $vo['id'] . '"' . ',"edit_id":"' . $vo['id'] . '"' . ',"type":"' . $vo['type'] . '"' . ',"en_type":"' . $vo['en_type'] . '"';
                                $str .= '},';
                            }
                        } else if ($type == 4) {
                            if ($vo['type'] <= 4) {
                                $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['d_name'] . '"' . ',"d_code":"' . $vo['d_code'] . '"' . ',"section_id":"' . $vo['section_id'] . '"' . ',"add_id":"' . $vo['id'] . '"' . ',"edit_id":"' . $vo['id'] . '"' . ',"type":"' . $vo['type'] . '"' . ',"en_type":"' . $vo['en_type'] . '"';
                                $str .= '},';
                            }
                        } else {
                            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['d_name'] . '"' . ',"d_code":"' . $vo['d_code'] . '"' . ',"section_id":"' . $vo['section_id'] . '"' . ',"add_id":"' . $vo['id'] . '"' . ',"edit_id":"' . $vo['id'] . '"' . ',"type":"' . $vo['type'] . '"' . ',"en_type":"' . $vo['en_type'] . '"';
                            $str .= '},';
                        }
                    }
                }
            }
        }
        return "[" . substr($str, 0, -1) . "]";
    }

    public function isParent($id)
    {
        $is_exist = $this->where('pid', $id)->find();
        return $is_exist;
    }


    public function insertTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            $id = $this->getLastInsID();
            $data = $this->getOne($id);
            $data['name'] = $data['d_name'];
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => $data, 'msg' => '添加成功'];
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

    public function deleteTb($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function getOne($id)
    {
        $data = $this->find($id);
        return $data;
    }

    public function getEnType()
    {
        $data = Db::name('materialtrackingdivision')->where(['cat' => 5, 'type' => ['lt', 3]])->column('id,pid,name');
        $str = '';
        foreach ($data as $v) {
            $str .= '{ "id": "' . $v['id'] . '", "pId":"' . $v['pid'] . '", "name":"' . $v['name'] . '"';
            $str .= '},';
        }
        return "[" . substr($str, 0, -1) . "]";
    }

}