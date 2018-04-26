<?php
//主页，控制面板

namespace app\admin\controller;

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
        // 新增 前台需要传递 的是  模型图主键 picture_id 属性名 attrKey  属性值 attrVal
        // 编辑 前台需要传递 的是  模型图主键 picture_id 属性名 attrKey  属性值 attrVal   和 这条属性的主键 attrId
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
            $id = $param['attrId'];
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
                array_push($rule,['create_time', 'require|date', '创建时间不能为空|时间格式有误']);
                array_push($rule,['remark', 'require|date', '备注不能为空']);
            }
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }
            $data['type'] = $param['type'];
            $data['picture_id'] = $param['picture_id'];
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
     * 创建人 和 创建日期
     * @return \think\response\Json
     * @author huao
     */
    public function labelAttr()
    {
         // 前台什么也不用传递
        if($this->request->isAjax()){
            $user_id = Session::get('admin');
            $data['user_name'] = Db::name('admin')->where('id',$user_id)->value('name');
            $data['create_time'] = date('Y-m-d H;i:s');
            return json(['code'=>1,'data'=>$data,'msg'=>'创建人和创建时间']);
        }
    }

    public function anchorPoint()
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
            $name = '';
        }
    }


}
