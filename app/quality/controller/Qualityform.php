<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/16
 * Time: 14:45
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;
use app\archive\model\AtlasCateModel;
use app\contract\model\ContractModel;
use app\quality\model\DivisionControlPointModel;
use app\quality\model\DivisionModel;
use app\quality\model\DivisionUnitModel;
use think\Request;

/**
 * 在线填报
 * Class Qualityform
 * @package app\quality\model
 */
class Qualityform extends Permissions
{
    protected $divisionControlPointService;
    protected $atlasCateService;
    protected $divisionUnitService;

    public function __construct(Request $request = null)
    {
        $this->divisionControlPointService = new DivisionControlPointModel();
        $this->atlasCateService = new AtlasCateModel();
        $this->divisionUnitService = new DivisionUnitModel();
        parent::__construct($request);
    }

    /**
     * 编辑质量表单
     * @param $cpr_id 控制点
     */
    public function edit($cpr_id, $currentStep, $isView = false)
    {
        //获取模板路径
        //获取控制点信息，组合模板路径
        $cp = $this->divisionControlPointService->with('ControlPoint')->where('id', $cpr_id)->find();
        $formPath = ROOT_PATH . 'public' . DS . "data\\form\\quality\\" . $cp['ControlPoint']['code'] . $cp['ControlPoint']['name'] . ".html";
        $formPath = iconv('UTF-8', 'GB2312', $formPath);
        if (!file_exists($formPath)) {
            return "模板文件不存在";
        }
        $htmlContent = file_get_contents($formPath);
        //$htmlContent =   str_replace("{{id}}" );
        //$htmlContent =   str_replace("{{templateId}}");
        $htmlContent = str_replace('{divisionId}', $cp['division_id'], $htmlContent);
        $htmlContent = str_replace('{isInspect}', $cp['type'] ? 'true' : 'false', $htmlContent);
        $htmlContent = str_replace('{procedureId}', $cp['ma_division_id'], $htmlContent);
        $htmlContent = str_replace('{controlPointId}', $cp['control_id'], $htmlContent);
        $htmlContent = str_replace('{formName}', $cp['ControlPoint']['name'], $htmlContent);
        $htmlContent = str_replace('{currentStep}', $currentStep, $htmlContent);
        $htmlContent = str_replace('{isView}', $isView, $htmlContent);

        //输出模板内容
        //Todo 暂时使用replace替换，后期修改模板使用fetch自定义模板渲染
        return $this->setFormInfo($cp['division_id'], $htmlContent);
    }

    /**
     * 设置表单基本信息
     * @param $qualityUnit_id 检验批
     */
    protected function setFormInfo($qualityUnit_id, $htmlContent)
    {
        $mod = $this->divisionUnitService->with("Division.Section")->where(['id' => $qualityUnit_id])->find();
        $output = array();
        $output['JYPName'] = $mod['site'];
        $output['JYPCode'] = $output['JJCode'] = $mod['coding'];
        $output['Quantity'] = $mod['quantities'];
        $output['PileNo'] = $mod['pile_number'];
        $output['Altitude'] = $mod['el_start'] . $mod['el_cease'];
        $output['BuildBase'] = $mod['ma_bases'] ? "" : $this->getBuildBaseInfo($mod['ma_bases']) . $mod['su_basis'];
        $output['DYName'] = $mod['Division']['d_name'];
        $output['DYCode'] = $mod['Division']['d_code'];
        //标段信息
        if ($mod['Division']['Section'] != null) {
            $_section = $mod['Division']['Section'];
            $output['Constructor'] = $_section['constructorId'] ? AdminGroup::get($_section['constructorId'])['name'] : "";
            $output ['Supervisor'] = $_section['supervisorId'] ? AdminGroup::get($_section['supervisorId'])['name'] : "";
            $output ['SectionCode'] = $_section['code'];
            $output['SectionName'] = "丰宁抽水蓄能电站";
            $output['ContractCode'] = $_section['contractId'] ? ContractModel::get($_section['contractId'])['contractName'] : "";
        }
        $Info = $this->getDivsionInfo($mod['division_id']);
        $output['FBName']=$Info['FB']['d_name'];
        $output['FBCode']=$Info['FB']['d_code'];
        $output['DWName']=$Info['DW']['d_name'];
        $output['DWCode']=$Info['DW']['d_code'];
        foreach ($output as $key => $value) {
            $htmlContent = str_replace("{{$key}}", $value, $htmlContent);
        }
        return $htmlContent;
    }

    /**
     * 获取工程依据信息
     * @param $ids
     * @return string
     */
    function getBuildBaseInfo($ids)
    {
        $idArr = explode(',', $ids);
        $_str = "";
        foreach ($this->atlasCateService->whereIn('id', $idArr) as $item) {
            $_str .= $item['picture_name'] . "({$item['picture_number']})";
        }
        return $_str;
    }

    /**
     * 获取单元及分布信息
     * @param $id
     */
    function getDivsionInfo($id)
    {
        $__mod = DivisionModel::get($id);
        $_mod = array();

        $_mod['FB'] = DivisionModel::get($__mod['pid']);
        $_mod['DW'] = DivisionModel::get($_mod['FB']['pid']);
        return $_mod;
    }
}