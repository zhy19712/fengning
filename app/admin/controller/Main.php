<?php
//主页，控制面板

namespace app\admin\controller;

use app\quality\model\AnchorPointModel;
use app\quality\model\CustomAttributeModel;
use app\quality\model\LabelSnapshotModel;
use app\quality\model\PictureModel;
use think\Db;
use think\Session;

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
        // 新增 前台需要传递 的是  模型图编号 picture_id 属性名 attrKey  属性值 attrVal
        // 编辑 前台需要传递 的是  模型图编号 picture_id 属性名 attrKey  属性值 attrVal   和 这条属性的主键 attrId
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
            $data['picture_number'] = $param['picture_id'];
            $data['attr_name'] = $param['attrKey'];
            $data['attr_value'] = $param['attrVal'];
            $custom = new CustomAttributeModel();
            $id = isset($param['attrId']) ? $param['attrId'] : 0;
            if(empty($id)){
                $flag = $custom->insertTb($data);
            }else{
                if(!is_int($id)){
                    return json(['code' => -1, 'msg' => '属性的编号只能是数字']);
                }
                $data['id'] = $id;
                $flag = $custom->editTb($data);
            }
            return json($flag);
        }
    }

    /**
     * 删除属性
     * @return \think\response\Json
     * @author hutao
     */
    public function delAttr()
    {
        // 前台只需要给我传递 要删除的 属性的主键 attrId
        $param = input('param.');
        // 验证规则
        $rule = [
            ['attrId', 'require|number|gt:-1', '请选择要删除的属性|属性编号只能是数字|属性编号不能为负数']
        ];
        $validate = new \think\Validate($rule);
        //验证部分数据合法性
        if (!$validate->check($param)) {
            return json(['code' => -1,'msg' => $validate->getError()]);
        }
        $node = new CustomAttributeModel();
        $flag = $node->deleteTb($param['attrId']);
        return json($flag);
    }

    /**
     * 回显自定义属性
     * @return \think\response\Json
     * @author hutao
     */
    public function getAttr()
    {
        // 前台需要传递 的是  模型图编号 picture_id
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
        // 前台需要传递 的是  模型图编号 picture_id 描述 remark
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
            // 1工程划分模型 2 建筑模型 3三D模型
            $data['id'] = Db::name('quality_model_picture')->where(['picture_type'=>1,'picture_number'=>$param['picture_id']])->value('id');
            if(empty($data['id'])){
                return json(['code'=>1,'msg'=>'不存在的模型编号']);
            }
            $data['remark'] = $param['remark'];
            $pic = new PictureModel();
            $pic->editTb($data);
            return json(['code'=>1,'msg'=>'操作成功']);
        }
    }

    /**
     * 获取模型图描述
     * @return \think\response\Json
     * @author hutao
     */
    public function getRemark()
    {
        // 前台需要传递 的是  模型图编号 picture_id
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
            // 1工程划分模型 2 建筑模型 3三D模型
            $id = Db::name('quality_model_picture')->where(['picture_type'=>1,'picture_number'=>$param['picture_id']])->value('id');
            if(empty($id)){
                return json(['code'=>1,'msg'=>'不存在的模型编号']);
            }
            $remark = $pic->getRemarkTb($id);
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
        // 前台需要传递 的是  模型图编号 picture_id 类型 type 1标注2快照  图片的base64值 label_snapshot
        // 注意：：：如果是标注 那么 就还要 传递 user_name 创建人 create_time 创建时间 remark 备注
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['type', 'require|number|between:1,2', '类型值不能为空|类型值只能是数字|类型值是1或者2'],
                ['label_snapshot', 'require', '图片值不能为空']
            ];
            // 类型 type 1标注2快照
            if(!empty($param['type']) && $param['type'] == 1){
                array_push($rule,['user_name', 'require', '创建人不能为空']);
                array_push($rule,['create_time', 'require|dateFormat:Y-m-d H:i:s', '创建时间不能为空|时间格式有误']);
                array_push($rule,['remark', 'require', '备注不能为空']);
            }
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $data['type'] = $param['type'];
            $data['picture_number'] = $param['picture_id'];
            $data['label_snapshot'] = $param['label_snapshot'];
            $user_id = Session::get('admin');
            $data['user_name'] = Db::name('admin')->where('id',$user_id)->value('name');
            // 类型 type 1标注2快照
            if($data['type'] == 1){
                $data['create_time'] = strtotime($param['create_time']);
                $data['remark'] = $param['remark'];
            }
            $pic = new LabelSnapshotModel();
            $flag = $pic->insertTb($data);
            return json($flag);
        }
    }

    /**
     * 删除标注或者快照
     * @return \think\response\Json
     * @author hutao
     */
    public function delLabelSnapshot()
    {
        // 前台需要传递 的是  标注快照的主键 label_snapshot_id
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['label_snapshot_id', 'require|number|gt:-1', '请选择要删除的标注或者快照|标注快照的编号只能是数字|标注快照的编号不能为负数']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $id = $param['label_snapshot_id'];
            $pic = new LabelSnapshotModel();
            $flag = $pic->deleteTb($id);
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

    /**
     * 创建人 和 创建日期 和 关联构件名称
     * @return \think\response\Json
     * @author huao
     */
    public function labelAttr()
    {
         // 前台什么也不用传递
        // 当前台传递 模型图主键 picture_id 类型 type 等于 3 时 是锚点的请求 后台 返回 创建人 创建日期 关联构件名称
        if($this->request->isAjax()){
            $param = input('param.');
            $user_id = Session::get('admin');
            $data['user_name'] = Db::name('admin')->where('id',$user_id)->value('name');
            $data['create_time'] = date('Y-m-d H:i:s');
            if(empty($param['type'])){
                return json(['code'=>1,'data'=>$data,'msg'=>'创建人和创建时间']);
            }else{
                // 验证规则
                $rule = [
                    ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                    ['type', 'require|number', '类型值不能为空|类型值只能是数字']
                ];
                $validate = new \think\Validate($rule);
                //验证部分数据合法性
                if (!$validate->check($param)) {
                    return json(['code' => -1,'msg' => $validate->getError()]);
                }
                // 1工程划分模型 2 建筑模型 3三D模型
                $data['componentName'] = Db::name('quality_model_picture')->where(['picture_type'=>1,'picture_number'=>$param['picture_id']])->value('picture_name');
                return json(['code'=>1,'data'=>$data,'msg'=>'创建人,创建时间和关联构件名称']);
            }
        }
    }

    /**
     * 添加锚点
     * @return \think\response\Json
     * @author hutao
     */
    public function anchorPoint()
    {
        // 前台需要传递 的是  模型图编号 picture_id 锚点名称 anchorName 创建人 user_name 创建日期 create_time 关联构件名称 componentName 备注信息 remark 位置 fObjSelX fObjSelY fObjSelZ
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['picture_id', 'require|number|gt:-1', '请选择模型图|模型图编号只能是数字|模型图编号不能为负数'],
                ['anchorName', 'require', '锚点名称不能为空'],
                ['user_name', 'require', '创建人不能为空'],
                ['create_time', 'require|dateFormat:Y-m-d H:i:s', '创建时间不能为空|时间格式有误'],
                ['componentName', 'require', '关联构件名称不能为空'],
                ['fObjSelX', 'require', 'X坐标不能为空'],
                ['fObjSelY', 'require', 'Y坐标不能为空'],
                ['fObjSelZ', 'require', 'Z坐标不能为空']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            // 1工程划分模型 2 建筑模型 3三D模型
            $data['picture_number'] = $param['picture_id'];
            $data['anchor_name'] = $param['anchorName'];
            $data['user_name'] = $param['user_name'];
            $data['create_time'] = strtotime($param['create_time']);
            $data['component_name'] = $param['componentName'];
            $data['coordinate_x'] = $param['fObjSelX'];
            $data['coordinate_y'] = $param['fObjSelY'];
            $data['coordinate_z'] = $param['fObjSelZ'];
            $anchor = new AnchorPointModel();
            $flag = $anchor->insertTb($data);
            return json($flag);
        }
    }

    /**
     * 删除锚点
     * @return \think\response\Json
     * @author hutao
     */
    public function delAnchorPoint()
    {
        // 前台需要传递 的是 锚点的主键 anchor_point_id
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['anchor_point_id', 'require|number|gt:-1', '请选择要删除的标注或者快照|标注快照的编号只能是数字|标注快照的编号不能为负数']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $id = $param['anchor_point_id'];
            $pic = new AnchorPointModel();
            $flag = $pic->deleteTb($id);
            return json($flag);
        }
    }

    /**
     * 上传附件
     * @return \think\response\Json
     * @author hutao
     */
    public function uploadAnchorPoint()
    {
        // 前台需要 传递 锚点的主键 anchor_point_id 上传的文件 file
        // 执行上传文件 获取文件编号  attachment_id
        $common = new \app\admin\controller\Common();
        $attachment_id = $common->upload('quality','anchor_point');
        // 保存上传文件记录
        $param = input('param.');
        // 验证规则
        $rule = [
            ['anchor_point_id', 'require|number|gt:-1', '请选择要删除的标注或者快照|标注快照的编号只能是数字|标注快照的编号不能为负数']
        ];
        $validate = new \think\Validate($rule);
        //验证部分数据合法性
        if (!$validate->check($param)) {
            return json(['code' => -1,'msg' => $validate->getError()]);
        }
        $data['id'] = $param['anchor_point_id'];
        $data['attachment_id'] = $attachment_id;
        $unit = new AnchorPointModel();
        $nodeStr = $unit->editTb($data);
        return json($nodeStr);
    }

    /**
     * 单个回显锚点
     * @return \think\response\Json
     * @author hutao
     */
    public function getAnchorPoint()
    {
        // 前台需要传递 的是 锚点的名称 anchorName
        if($this->request->isAjax()){
            $param = input('param.');
            // 验证规则
            $rule = [
                ['anchorName', 'require', '请选择要回显的锚点名称']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $name = $param['anchorName'];
            $pic = new AnchorPointModel();
            $flag = $pic->getAnchorTb($name);
            return json($flag);
        }
    }

    /**
     * 全部回显锚点
     * @return \think\response\Json
     * @author hutao
     */
    public function allAnchorPoint()
    {
        // 前台需要传递 的是 锚点的名称 anchorName
        if($this->request->isAjax()){
            $pic = new AnchorPointModel();
            $flag = $pic->getAnchorTb();
            return json($flag);
        }
    }

}
