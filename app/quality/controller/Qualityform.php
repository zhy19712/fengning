<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/16
 * Time: 14:45
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\DivisionControlPointModel;
use think\Request;

/**
 * 在线填报
 * Class Qualityform
 * @package app\quality\model
 */
class Qualityform extends Permissions
{
    protected $divisionControlPointService;

    public function __construct(Request $request = null)
    {
        $this->divisionControlPointService = new DivisionControlPointModel();
        parent::__construct($request);
    }

    /**
     * 编辑质量表单
     * @param $cpr_id 控制点
     */
    public function edit($cpr_id)
    {
        //获取模板路径
        //获取控制点信息，组合模板路径
        $cp = $this->divisionControlPointService->with('ControlPoint')->where('id', $cpr_id)->find();
        $formPath = ROOT_PATH . 'public' . DS . "data\\form\\quality\\" . $cp['ControlPoint']['code'] . $cp['ControlPoint']['name'] . ".html";
        $formPath =iconv('UTF-8', 'GB2312', $formPath);
        return file_get_contents($formPath);
        return json(['is' => file_exists($formPath)]);
        return $formPath;
        if (!file_exists($formPath)) {
            return "模板文件不存在";
        }
        $htmlContent = file_get_contents($formPath);
        $htmlContent = str_replace();
        $htmlContent = $htmlContent . str_replace("{{id}}", id . ToString());
        $htmlContent = $htmlContent . str_replace("{{templateId}}", templateId . ToString());
        $htmlContent = $htmlContent . str_replace("{{divisionId}}", divisionId . ToString());
        $htmlContent = $htmlContent . str_replace("{{isInspect}}", isInspect == 1 ? "true" : "false");
        $htmlContent = $htmlContent . str_replace("{{procedureId}}", procedureId . ToString());
        $htmlContent = $htmlContent . str_replace("{{controlPointId}}", controlPointId . ToString());
        $htmlContent = $htmlContent . str_replace("{{formName}}", templateDto . Name);
        $htmlContent = $htmlContent . str_replace("{{currentStep}}", currentStep . ToString());
        $htmlContent = $htmlContent . str_replace("{{isView}}", isView . ToString());
        return json(file_exists($formPath));
        //输出模板内容

    }
}