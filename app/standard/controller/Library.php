<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/8
 * Time: 11:22
 */

namespace app\standard\controller;

use app\admin\controller\Permissions;
use app\standard\model\ControlPoint;
use app\standard\model\MaterialTrackingDivision;
use think\Request;

/**
 * 标准库
 * Class Library
 * @package app\standard\controller
 */
class Library extends Permissions
{
    protected $controlPointService;
    protected $materialTrackingDivesionService;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->materialTrackingDivesionService = new MaterialTrackingDivision();
        $this->controlPointService = new ControlPoint();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function com()
    {
        return $this->fetch();
    }

    public function branch()
    {
        return $this->fetch();
    }

    public function item()
    {
        return $this->fetch();
    }

    public function unit()
    {
        return $this->fetch();
    }

    public function special()
    {
        return $this->fetch();
    }

    /**
     * 新增编辑控制点
     * @return mixed
     */
    public function addcontrollpoint($id = null)
    {
        if ($this->request->isAjax()) {
            $mod = input('post.');
            if (empty($mod['id'])) {
                $res = $this->controlPointService->allowField(true)->save($mod);
            } else {
                $res = $this->controlPointService->allowField(true)->save($mod, ['id' => $mod['id']]);
            }
            return $res ? json(['code' => 1]) : json(['code' => -1]);
        }

        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 编辑划分树
     */
    public function adddivsiontree()
    {
        $mod = input('post.');
        if (empty($mod['id'])) {
            $res = $this->materialTrackingDivesionService->allowField(true)->save($mod);
        } else {
            $res = $this->materialTrackingDivesionService->allowField(true)->save($mod, ['id' => $mod['id']]);
        }
    }

    /**
     * 标准库划分树
     * @param $cat 标准库分类
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public function GetDivsionTree($cat)
    {
        return MaterialTrackingDivision::all(['cat' => $cat]);
    }

    /**
     * 选择模板
     * @return mixed
     */
    public function chosetemplate()
    {
        return $this->fetch();
    }

    /**
     * 获取控制点
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getcontrolpoint($id)
    {
        return json(ControlPoint::get($id));
    }

    /**
     * 删除控制点
     * @param $id
     * @return \think\response\Json
     */
    public function delcontrolpoint($id)
    {
        if (ControlPoint::destroy($id)) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }
}