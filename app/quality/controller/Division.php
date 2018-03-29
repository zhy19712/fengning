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
     * 新增 或者 编辑 组织机构的节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function editNode()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $param = input('post.');
            // 验证规则
            if(empty($param['type'])){
                $validate = new \think\Validate([
                    ['name', 'require|max:100', '部门名称不能为空|名称不能超过100个字符'],
                    ['pid', 'require', '请选择组织机构'],
                ]);
            }else{
                $validate = new \think\Validate([
                    ['name', 'require|max:100', '机构名称不能为空|名称不能超过100个字符'],
                    ['type', 'require', '请选择机构类型'],
                ]);
            }
            //验证部分数据合法性
            if (!$validate->check($param)) {
                $this->error('提交失败：' . $validate->getError());
            }

            /**
             * 当新增 机构的时候
             * 前台需要传递的是 pid 父级节点编号,type 机构类型,name 节点名称
             * 编辑 机构的时候 传递 id 自己的编号 pid 父级节点编号,type 机构类型,name 节点名称
             *
             * 当新增 部门的时候
             * 前台需要传递的是 pid 父级节点编号,name 节点名称
             * 编辑 机构的时候 传递 id 自己的编号 pid 父级节点编号,name 节点名称
             *
             * 系统自动判断赋值 category 1 组织机构 2 部门
             */
            if(empty($param['type'])){
                $data = ['pid' => $param['pid'],'category' => '2','name' => $param['name']];
            }else{
                $data = ['pid' => $param['pid'],'category' => '1','type' => $param['type'],'name' => $param['name']];
            }
            if(empty($param['id'])){
                $flag = $node->insertTb($data);
                return json($flag);
            }else{
                $data['id'] = $param['id'];
                $flag = $node->editTb($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除 组织机构的节点
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author hutao
     */
    public function delNode()
    {
        /**
         * 前台只需要给我传递 要删除的 节点自己的id 编号
         */
        $param = input('post.');
        $node = new DivisionModel();
        // 是否包含子节点
        $exist = $node->isParent($param['id']);
        if(!empty($exist)){
            return json(['code' => -1,'msg' => '包含子节点,不能删除']);
        }

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