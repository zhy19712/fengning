<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/8
 * Time: 9:06
 */
namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\ScenePictureModel;//现场图片模型

use \think\Db;
use \think\Session;

/**
 * 日常质量管理，图片管理
 * Class Atlas
 * @package app\quality\controller
 */
class Scenepicture extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************现场图片类型树************************/
    /**
     * 现场图片类型树
     * @return mixed|\think\response\Json
     */
    public function picturetree()
    {
        if ($this->request->isAjax()) {
            //实例化模型ScenePictureModel
            $model = new ScenePictureModel();
            //查询fengning_scene_picture现场图片表
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