<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/13
 * Time: 11:26
 */
/**
 * 质量管理-分部质量管理
 * Class Branch
 * @package app\quality\controller
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;
use app\quality\model\BranchModel;//分部质量管理
use \think\Session;
use think\Db;

class Branch extends Permissions
{
    /****************************分部策划************************/
    /**
     * 分部策划模板首页
     * @return mixed
     */
    public function plan()
    {
        return $this->fetch();

    }

    /**
     * 获取分部质量管理-控制点
     * @return \think\response\Json
     */
    public function getControlPoint()
    {
        $data = Db::name('materialtrackingdivision')->where(['type'=>3,'cat'=>3])->column('id,name');
        if($data)
        {
            return json(['code'=>1,'data'=>$data]);
        }else
        {
            return json(['code'=>-1,'data'=>""]);
        }
    }

    /**
     * 分部策划列表-导出二维码
     * @return \think\response\Json
     */
    public function exportCode()
    {

    }

    /**
     * 控制点里的模板文件下载
     * @return \think\response\Json
     */
    public function fileDownload()
    {
        // 前台需要 传递 文件编号 id
        $param = input('param.');
        $file_id = isset($param['id']) ? $param['id'] : 0;
        if($file_id){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        $file_obj = Db::name('attachment')->where('id',$file_id)->field('filename,filepath')->find();
        $filePath = '.' . $file_obj['filepath'];
        if(!file_exists($filePath)){
            return json(['code' => '-1','msg' => '文件不存在']);
        }else if(request()->isAjax()){
            return json(['code' => 1]); // 文件存在，告诉前台可以执行下载
        }else{
            $fileName = $file_obj['filename'];
            $file = fopen($filePath, "r"); //   打开文件
            //输入文件标签
            $fileName = iconv("utf-8","gb2312",$fileName);
            Header("Content-type:application/octet-stream ");
            Header("Accept-Ranges:bytes ");
            Header("Accept-Length:   " . filesize($filePath));
            Header("Content-Disposition:   attachment;   filename= " . $fileName);
            //   输出文件内容
            echo fread($file, filesize($filePath));
            fclose($file);
            exit;
        }
    }

    /**
     * 控制点里的模板文件打印
     * @return \think\response\Json
     */
    public function  printDocument()
    {

    }

    /**
     * 删除控制点
     *
     * 控制点 存在于 controlpoint 表 和 fengning_quality_subdivision_planning_list 中
     * controlpoint 表里的数据 是原始的，fengning_quality_subdivision_planning_list 是 在 分部策划里 后来 新增的关系记录
     *
     * 如果关系记录存在 该控制点 那么就应该先
     * 要关联 删除 记录里的控制点执行情况 和 图像资料  以及它们所包含的文件 以及 预览的pdf文件
     * 然后 删除 这条关系记录
     *
     * 最后 删除 原始数据
     *
     * type 类型:1 检验批 0 工程划分
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function controlDel()
    {
//        if(request()->isAjax()){
            //实例化模型类
            $model = new BranchModel();
            //分部策划列表id
            $id = input('param.id');
            $id = 1;
            //查询一条分部策划列表中的信息
            $info = $model->getOne($id);
            if($info)
            {
                $selfid = $info["selfid"];//工程划分树的节点id
                $controller_point_id = $info["controller_point_id"];//控制点id
                $flag = $model->associationDeletion($id,$selfid,$controller_point_id);
                return json($flag);
            }
//        }
    }



    /****************************分部管控************************/
    /**
     * 分部管控模板首页
     * @return mixed
     */
    public function control()
    {
        return $this->fetch();

    }
}