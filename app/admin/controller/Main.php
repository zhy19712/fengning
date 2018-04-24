<?php
//主页，控制面板

namespace app\admin\controller;

use app\quality\model\CustomAttributeModel;
use app\quality\model\PictureModel;

class Main extends Permissions
{
    public function index()
    {

        return $this->fetch();
    }

    public function selectperson()
    {
        return $this->fetch();
    }

    /**
     * 管理属性 -- 添加 自定义属性
     * @return string|\think\response\Json
     * @author hutao
     */
    public function addAttr()
    {
        // 前台需要传递 的是  模型图主键 picture_id 属性名数组 attrKey  属性值数组attrVal
        if($this->request->isAjax()){
            $param = input('param.');
            $picture_id = isset($param['picture_id']) ? $param['picture_id'] : -1;
            $attrKey = isset($param['attrKey']) ? $param['attrKey'] : -1;
            $attrVal = isset($param['attrVal']) ? $param['attrVal'] : -1;
            if($picture_id == -1 || $attrKey == -1 || $attrVal == -1){
                return join(['code'=>-1,'msg'=>'参数有误']);
            }
            $data = [];
            $data['picture_id'] = $picture_id;
            $data['attr_name'] = $attrKey;
            $data['attr_value'] = $attrVal;
            $custom = new CustomAttributeModel();
            $flag = $custom->insertTb($data);
            return json($flag);
        }
    }

    /**
     * 管理属性 -- 添加 描述
     * @return string|\think\response\Json
     * @author hutao
     */
    public function addRemark()
    {
        // 前台需要传递 的是  模型图主键 picture_id 属性名数组 attrKey  属性值数组attrVal
        if($this->request->isAjax()){
            $param = input('param.');
            $picture_id = isset($param['picture_id']) ? $param['picture_id'] : -1;
            $remark = isset($param['remark']) ? $param['remark'] : -1;
            if($picture_id == -1 || $remark == -1){
                return join(['code'=>-1,'msg'=>'参数有误']);
            }
            $data['picture_id'] = $picture_id;
            $data['remark'] = $remark;
            $pic = new PictureModel();
            $pic->insertTb($data);
            return json(['code'=>1,'msg'=>'添加成功']);
        }
    }

}
