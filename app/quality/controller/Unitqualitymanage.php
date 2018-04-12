<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/11
 * Time: 14:17
 */

namespace app\quality\controller;


use app\admin\controller\Permissions;
use app\quality\model\DivisionModel;
use app\quality\model\UnitqualitymanageModel;
use app\standard\model\ControlPoint;
use think\Db;

// 单位质量管理

class Unitqualitymanage extends Permissions
{
    /**
     * 单位策划 或者 单位管控 初始化左侧树节点
     * @param int $type
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function index($type = 1)
    {
        if($this->request->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo(2); // 2 只取到子单位工程
            return json($nodeStr);
        }
        if($type==1){
            return $this->fetch();
        }
        return $this->fetch('control');
    }

    /**
     * 获取节点工序
     * @return \think\response\Json
     * @author hutao
     */
    public function productionProcesses()
    {
        $data = Db::name('materialtrackingdivision')->where(['type'=>3,'cat'=>2])->column('id,name');
        $data[0] = '作业';
        return json(['code' => 1,'data' => $data,'msg' => '工序']);
    }

    //TODO 导出二维码 预留接口
    public function exportCode()
    {

    }


    //TODO 此处下载的是 控制点里 的模板文件 预留接口
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

    // TODO 打印文件 预留接口
    public function  printDocument()
    {

    }

    /**
     * 删除控制点
     *
     * 控制点 存在于 controlpoint 表 和 quality_division_controlpoint_relation 中
     * controlpoint 表里的数据 是原始的，quality_division_controlpoint_relation 是 在 单位策划里 后来 新增的关系记录
     *
     * 如果关系记录存在 该控制点 那么就应该先
     * 要关联 删除 记录里的控制点执行情况 和 图像资料  以及它们所包含的文件 以及 预览的pdf文件
     * 然后 删除 这条关系记录
     *
     * 最后 删除 原始数据
     *
     * type 类型:1 检验批 0 工程划分
     * @return \think\response\Json
     * @author hutao
     */
    public function controlDel()
    {
        // 前台需要 传递 节点编号 add_id 工序编号 ma_division_id 控制点编号 id
        $param = input('param.');
        $add_id = isset($param['add_id']) ? $param['add_id'] : 0;
        $ma_division_id = isset($param['ma_division_id']) ? $param['ma_division_id'] : -1; // 工序作业编号是0
        $id = isset($param['id']) ? $param['id'] : 0;
        if($add_id || ($ma_division_id == -1) || $id){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        if(request()->isAjax()) {
            $unit = new UnitqualitymanageModel();
            $flag = $unit->associationDeletion($add_id,$ma_division_id,$id);
            return json($flag);
        }
    }

    // 添加控制点
    public function addControl()
    {

    }


    // 控制点执行情况文件 或者 图像资料文件 上传保存
    public function editRelation()
    {

    }

    // 控制点执行情况文件 或者 图像资料文件 预览
    public function relationPreview()
    {
        if(request()->isAjax()) {
            $param = input('post.');
            $code = 1;
            $msg = '预览成功';
            $data = Db::name('attachment')->where('id',$param['attachment_id'])->find();
            if(!$data['path'] || !file_exists("." .$data['path'])){
                return json(['code' => '-1','msg' => '文件不存在']);
            }
            $path = $data['path'];
            $extension = strtolower(get_extension(substr($path,1)));
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if(!file_exists($pdf_path)){
                if($extension === 'doc' || $extension === 'docx' || $extension === 'txt'){
                    doc_to_pdf($path);
                }else if($extension === 'xls' || $extension === 'xlsx'){
                    excel_to_pdf($path);
                }else if($extension === 'ppt' || $extension === 'pptx'){
                    ppt_to_pdf($path);
                }else if($extension === 'pdf'){
                    $pdf_path = $path;
                }else if($extension === "jpg" || $extension === "png" || $extension === "jpeg"){
                    $pdf_path = $path;
                }else {
                    $code = 0;
                    $msg = '不支持的文件格式';
                }
                return json(['code' => $code, 'path' => substr($pdf_path,1), 'msg' => $msg]);
            }else{
                return json(['code' => $code,  'path' => substr($pdf_path,1), 'msg' => $msg]);
            }
        }
    }

    // 控制点执行情况文件 或者 图像资料文件 下载
    public function relationDownload()
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

    // 控制点执行情况文件 或者 图像资料文件 删除
    public function relationDel()
    {
        // 前台需要 传递 文件编号 id
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        if($id){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        if(request()->isAjax()) {
            $sd = new ControlPoint();
            //TODO 关联删除 控制点执行情况文件 和 图像资料文件
            $flag = $sd->deleteTb($id);
            return json($flag);
        }
    }

}