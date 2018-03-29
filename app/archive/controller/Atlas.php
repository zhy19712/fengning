<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 9:19
 */
namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\AtlasType;
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

    /*
     * 图册分类树
     * @return mixed|\think\response\Json
     */
    public function atlastree()
    {
        if ($this->request->isAjax()) {
            //实例化角色类型AdminCateType
            $model = new AdminCateType();
            //查询fengning_admin_cate_type角色类型表
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $k => $v) {
                $v['id'] = strval($v['id']);
                $res[$k] = json_encode($v);
            }

            return json($res);
        }

    }
}