<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/9
 * Time: 10:33
 */
/**
 * 日常质量管理，监理日志
 * Class Supervisionlog
 * @package app\quality\controller
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\SupervisionLogModel;//监理日志模型
use \think\Session;

class Supervisionlog extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************监理日志类型树************************/
    /**
     * 监理日志类型树
     * @return mixed|\think\response\Json
     */
    public function tree()
    {
        if ($this->request->isAjax()) {
            //实例化模型ScenePictureModel
            $model = new SupervisionLogModel();
            //查询fengning_scene_picture现场图片表
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $a => $b) {
                $b['id'] = strval($b['id']);
                $res[$a] = json_encode($b);
            }

            return json($res);
        }
    }
}