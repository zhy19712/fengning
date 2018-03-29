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
        if ($this->request->isAjax()) {
            //实例化图册类型AtlasCateTypeModel
            $model = new AtlasCateTypeModel();
            //查询fengning_atlas_cate_type图册类型表
            $data = $model->getall();
            $res = tree($data);
            if($res)
            {
                foreach ((array)$res as $k => $v) {
                    $v['id'] = strval($v['id']);
                    $v['pid'] = strval($v['pid']);
                    $res[$k] = json_encode($v);
                }
            }
            return json($res);
        }
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
}