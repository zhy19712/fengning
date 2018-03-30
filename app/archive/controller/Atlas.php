<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 9:19
 */
namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\AtlasCateTypeModel;
/**
 * 图纸文档管理，图册管理
 * Class Atlas
 * @package app\archive\controller
 */
class Atlas extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 图册分类树
     * @return mixed|\think\response\Json
     */
    public function atlastree()
    {
        // 获取左侧的树结构
        if(request()->isAjax()){
            $node = new AtlasCateTypeModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
        return $this->fetch();
    }

    /**
     * 新增 或者 编辑 图册类型的节点树
     * @return mixed|\think\response\Json
     */
    public function editCatetype()
    {
        if(request()->isAjax()){
            $model = new AtlasCateTypeModel();
            $param = input('post.');
            /**
             * 前台需要传递的是 pid 父级节点编号,id图册类型树自增,name图册节点树分类名
             */
            if(empty($param['id']))//id为空时表示新增图册类型节点
            {
                $data = [
                    'pid' => $param['pid'],
                    'name' => $param['name']
                ];
                $flag = $model->insertCatetype($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'name' => $param['name']
                ];
                $flag = $model->editCatetype($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除图册类型的节点树
     * @return \think\response\Json
     */
    public function delCatetype()
    {
        if(request()->isAjax()){
            //实例化图册类型AtlasCateTypeModel
            $model = new AtlasCateTypeModel();
            $param = input('post.');
            //删除图册分类


            //删除上传的图片


            //删除图册类型树节点
            $flag = $model->delCatetype($param['id']);
            return json($flag);
        }else
        {
            return $this->fetch();
        }

    }

    /**
     * 上移下移
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function sortNode()
    {
        if(request()->isAjax()){
            $prev_id = $this->request->has('prev_id') ? $this->request->param('prev_id', 0, 'intval') : 0; // 前一个节点的编号 没有默认0
            $prev_sort_id = $this->request->has('prev_sort_id') ? $this->request->param('prev_sort_id', 0, 'intval') : 0; // 前一个节点的排序编号 没有默认0

            $id = input('post.id'); // 当前节点的编号
            $id_sort_id = input('post.id_sort_id'); // 当前节点的排序编号

            $next_id = $this->request->has('next_id') ? $this->request->param('next_id', 0, 'intval') : 0; // 后一个节点的编号 没有默认0
            $next_sort_id = $this->request->has('next_sort_id') ? $this->request->param('next_sort_id', 0, 'intval') : 0; // 后一个节点的排序编号 没有默认0

            // 下移
            if(empty($prev_id)){
                Db::name('admin_group')->where('id',$next_id)->update(['sort_id' => $id_sort_id]);
                Db::name('admin_group')->where('id',$id)->update(['sort_id' => $next_sort_id]);
            }else if(empty($next_id)){
                Db::name('admin_group')->where('id',$id)->update(['sort_id' => $prev_sort_id]);
                Db::name('admin_group')->where('id',$prev_id)->update(['sort_id' => $id_sort_id]);
            }

            return json(['code' => 1,'msg' => '成功']);
        }
        return $this->fetch();
    }
}