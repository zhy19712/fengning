<?php
//主页，控制面板

namespace app\admin\controller;

use app\quality\model\CustomAttributeModel;
use app\quality\model\LabelSnapshotModel;
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
        // 前台需要传递 的是  模型图主键 picture_id 属性名 attrKey  属性值 attrVal
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['attrKey', 'require', '属性名不能为空'],
                ['attrVal', 'require', '属性值不能为空']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $data = [];
            $data['picture_id'] = $param['picture_id'];
            $data['attr_name'] = $param['attrKey'];
            $data['attr_value'] = $param['attrVal'];
            $custom = new CustomAttributeModel();
            $flag = $custom->insertTb($data);
            return json($flag);
        }
    }

    /**
     * 获取自定义属性
     * @return \think\response\Json
     * @author hutao
     */
    public function getAttr()
    {
        // 前台需要传递 的是  模型图主键 picture_id
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $custom = new CustomAttributeModel();
            $flag = $custom->getAttrTb($param['picture_id']);
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
        // 前台需要传递 的是  模型图主键 picture_id 描述 remark
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['remark', 'require', '描述不能为空']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $data['id'] = $param['picture_id'];
            $data['remark'] = $param['remark'];
            $pic = new PictureModel();
            $pic->editTb($data);
            return json(['code'=>1,'msg'=>'添加成功']);
        }
    }

    /**
     * 获取模型图描述
     * @return \think\response\Json
     * @author hutao
     */
    public function getRemark()
    {
        // 前台需要传递 的是  模型图主键 picture_id
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $pic = new PictureModel();
            $remark = $pic->getRemarkTb($param['picture_id']);
            return json(['code'=>1,'remark'=>$remark,'msg'=>'模型图描述']);
        }
    }

    /**
     * 添加标注或者快照
     * @return \think\response\Json
     * @author hutao
     */
    public function labelSnapshot()
    {
        // 前台需要传递 的是  模型图主键 picture_id 类型 type 1标注2快照  图片的base64值 label_snapshot
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['type', 'require|number|between:1,2', '类型值不能为空|类型值只能是数字|类型值是1或者2'],
                ['label_snapshot', 'require', '图片值不能为空']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $data['type'] = $param['type'];
            $data['picture_id'] = $param['picture_id'];
            $data['label_snapshot'] = $param['label_snapshot'];
            $pic = new LabelSnapshotModel();
            $flag = $pic->insertTb($data);
            return json($flag);
        }
    }

    /**
     * 回显标注或者快照
     * @return \think\response\Json
     * @author hutao
     */
    public function getLabelSnapshot()
    {
        // 前台需要传递 的是  模型图主键 picture_id 类型 type 1标注2快照
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['type', 'require|number|between:1,2', '类型值不能为空|类型值只能是数字|类型值是1或者2']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $pic = new LabelSnapshotModel();
            $flag = $pic->getLabelSnapshotTb($param['type'],$param['picture_id']);
            return json($flag);
        }
    }


}
