<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/27
 * Time: 16:16
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\DivisionModel;

/**
 * 工程划分
 * Class Division
 * @package app\quality\controller
 */
class Division extends Permissions{

    /**
     * 初始化左侧树节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function index()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
        return $this->fetch();
    }

    /**
     * 点击编辑的时候
     * 获取一条节点信息
     * @return \think\response\Json
     * @author hutao
     */
    public function getNode()
    {
        if(request()->isAjax()){
            $param = input('post.');
            $id = isset($param['id']) ? $param['id'] : 0;
            $node = new DivisionModel();
            $info['node'] = $node->getOne($id);
            return json($info);
        }
    }

    /**
     * 新增 或者 编辑 工程划分的节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function editNode()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $param = input('post.');
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $en_type = isset($param['en_type']) ? $param['en_type'] : '';
            // 验证规则
            $rule = [
                ['d_code', 'require|alphaDash', '编码不能为空|编码只能是字母、数字和破折号 - 的组合'],
                ['d_name', 'require|max:100', '名称不能为空|名称不能超过100个字符'],
                ['type', 'require|number', '请选择分类|分类只能是数字'],
                ['primary', 'require|number', '请选择是否是主要工程|是否是主要工程只能是数字']
            ];
            // 分类 1单位 2分部 3分项
            if($param['type'] != 3 && empty($en_type)){
                $validate = new \think\Validate($rule);
            }else{
                array_push($rule,['en_type', 'require|number', '请选择工程分类|工程类型只能是数字']);
                $validate = new \think\Validate($rule);
            }
            //验证部分数据合法性
            if (!$validate->check($param)) {
                $this->error('提交失败：' . $validate->getError());
            }

            /**
             * 节点 层级
             * 顶级节点 -》标段 -》单位工程 =》 子单位工程 =》分部工程 -》子分部工程 -》 分项工程 -》 单元工程 (注意 这是一条数据 是不在 树节点里的)
             *
             * 顶级节点 -》标段  不允许 增删改,它们是从其他表格获取的
             *
             * 当新增 单位工程 =》 子单位工程 =》分部工程 -》子分部工程 的时候
             * 前台需要传递的是 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             * 当新增 分项工程 的时候
             * 前台需要传递的是 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,en_type 工程分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             */
            $data = ['pid' => $param['pid'],'d_code' => $param['d_code'],'d_name' => $param['d_name'],'type' => $param['type'],'primary' => $param['primary'],'remark' => $param['remark']];
            if($param['type'] == 3 && !empty($en_type)){
                $data['en_type'] = $en_type;
            }
            if(empty($id)){
                $flag = $node->insertTb($data);
                return json($flag);
            }else{
                $data['id'] = $id;
                $flag = $node->editTb($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除 工程划分的节点
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author hutao
     */
    public function delNode()
    {
        /**
         * 前台只需要给我传递 要删除的 节点的 id 编号
         */
        $param = input('post.');
        $node = new DivisionModel();
        // 是否包含子节点
        $exist = $node->isParent($param['id']);
        if(!empty($exist)){
            return json(['code' => -1,'msg' => '包含子节点,不能删除']);
        }
        // 如果删除的是 分项工程 那么它 包含单元工程, 应该首先批量删除单元工程
        //Todo 如果 单元工程下面有 还包含其他的数据 那么 也要关联删除


        // 最后删除此节点
        $flag = $node->deleteTb($param['id']);
        return json($flag);
    }

    /**
     * 获取路径
     * @return \think\response\Json
     * @author hutao
     */
    public function getParents()
    {
        /**
         * 前台就传递 当前点击的节点的 id 编号
         */
        if(request()->isAjax()){
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $node = new DivisionModel();
            $path = "";
            while($id>0)
            {
                $data = $node->getOne($id);
                $path = $data['name'] . ">>" . $path;
                $id = $data['pid'];
            }
            return json(['code' => 1,'path' => substr($path, 0 , -2),'msg' => "success"]);
        }
    }
}