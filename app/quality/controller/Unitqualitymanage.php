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
     * 这里的 树节点 是从工程划分 树节点里取来的(而且只取到 子单位工程)
     *
     * 工序是从materialtrackingdivision表里取出来的 取的是 单位工程下的三级节点
     *
     * 注意:作业下的控制点 是 materialtrackingdivision 工序表 关联 controlpoint 控制点表 的全部数据
     *
     * 其他工序下的控制点 是 根据 quality_division_controlpoint_relation 对应关系表 关联 controlpoint 的数据
     * 可以新增，删除，全部删除，删除的是 对应关系表里的对应信息，不是真正的删除controlpoint里的数据
     *
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
     * 删除控制点 注意: 已经执行 的控制点 不能删除
     *
     * 控制点 存在于 quality_division_controlpoint_relation 中
     * quality_division_controlpoint_relation 里包含 新增控制点时 关联添加的对应关系
     * 和 在 单位策划里 后来 新增控制点 时 追加的 对应关系
     *
     * 如果关系记录存在 该控制点 那么就应该先
     * 要关联 删除 记录里的控制点执行情况 和 图像资料  以及它们所包含的文件 以及 预览的pdf文件
     * 最后 删除 这条关系记录
     *
     * type 类型:1 检验批 0 工程划分
     * @return \think\response\Json
     * @throws \think\Exception
     * @author hutao
     */
    public function controlDel()
    {
        // 前台需要 传递 节点编号 add_id 工序编号 ma_division_id 控制点编号 id
        $param = input('param.');
        $add_id = isset($param['add_id']) ? $param['add_id'] : 0;
        $ma_division_id = isset($param['ma_division_id']) ? $param['ma_division_id'] : -1; // 工序作业编号是0
        $id = isset($param['id']) ? $param['id'] : -1; // id 等于0 表示 全部删除
        if(($add_id == 0) || ($ma_division_id == -1) || ($id == -1)){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        if(request()->isAjax()) {
            $unit = new UnitqualitymanageModel();
            $flag = $unit->associationDeletion($add_id,$ma_division_id,$id);
            return json($flag);
        }
    }

    /**
     * 获取单位工程工序树
     * @return \think\response\Json
     * @author hutao
     */
    public function unitTree()
    {
        if($this->request->isAjax()){
            $node = new UnitqualitymanageModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
    }

    /**
     * 添加控制点
     * @return \think\response\Json
     * @author hutao
     */
    public function addControl()
    {
        // 前台需要 传递 节点编号 add_id 工序编号 ma_division_id
        $param = input('param.');
        $add_id = isset($param['add_id']) ? $param['add_id'] : 0;
        $ma_division_id = isset($param['ma_division_id']) ? $param['ma_division_id'] : 0; // 工序作业编号是0,但是作业没有添加方法
        $idArr = isset($param['idArr/a']) ? $param['idArr/a'] : 0;
        dump($add_id);
        dump($ma_division_id);
        halt($idArr);
        if(($add_id == 0) || ($ma_division_id == 0) || ($idArr == 0)){
            return json(['code' => -1 ,'msg' => '请选择需要新增的控制点']);
        }

        if($this->request->isAjax()){
            $data = [];
            foreach ($idArr as $k=>$v){
                $data[$k]['division_id'] = $add_id;
                $data[$k]['ma_division_id'] = $ma_division_id;
                $data[$k]['type'] = 0;
                $data[$k]['control_id'] = $v;
            }
            $unit = new UnitqualitymanageModel();
            $nodeStr = $unit->insertTb($data);
            return json($nodeStr);
        }
    }


    /**
     * 单位管控 控制点执行情况文件 或者 图像资料文件 上传保存
     * @return \think\response\Json
     * @author hutao
     */
    public function editRelation()
    {
        // 前台需要 传递 控制点编号 id 上传类型 type 1执行情况 2图像资料 文件编号  attachment_id
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        $type = isset($param['type']) ? $param['type'] : 0; // 1执行情况 2图像资料
        $attachment_id = isset($param['attachment_id']) ? $param['attachment_id'] : 0; // 文件编号
        if(($id == 0) || ($type == 0) || ($attachment_id == 0)){
            return json(['code' => '-1','msg' => '参数有误']);
        }
        if($this->request->isAjax()){
            $data['contr_relation_id'] = $id;
            $data['attachment_id'] = $attachment_id;
            $data['type'] = $type;
            $unit = new UnitqualitymanageModel();
            $nodeStr = $unit->saveTb($data);
            return json($nodeStr);
        }
    }

    /**
     * 控制点执行情况文件 或者 图像资料文件 预览
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author hutao
     */
    public function relationPreview()
    {
        // 前台 传递 id 编号
        $param = input('post.');
        $id = isset($param['id']) ? $param['id'] : 0;
        if($id  == 0){
            return json(['code' => 1,  'path' => '', 'msg' => '编号有误']);
        }
        if(request()->isAjax()) {
            $code = 1;
            $msg = '预览成功';
            $attachment_id = Db::name('quality_upload')->where('contr_relation_id',$id)->value('attachment_id');
            $data = Db::name('attachment')->where('id',$attachment_id)->find();
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

    /**
     * 控制点执行情况文件 或者 图像资料文件 下载
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author hutao
     */
    public function relationDownload()
    {
        // 前台需要 传递 id 编号
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        if($id == 0){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        $attachment_id = Db::name('quality_upload')->where('contr_relation_id',$id)->value('attachment_id');
        $file_obj = Db::name('attachment')->where('id',$attachment_id)->field('filename,filepath')->find();
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
     * 控制点执行情况文件 或者 图像资料文件 删除
     * 首先 删除 上传的文件 和 预览 的pdf 文件
     * 然后 删除 上传记录
     * 最后 删除 对应关系表记录
     * @return \think\response\Json
     * @throws \think\Exception
     * @author hutao
     */
    public function relationDel()
    {
        // 前台需要 传递 id 编号
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        if($id == 0){
            return json(['code' => '-1','msg' => '编号有误']);
        }
        if(request()->isAjax()) {
            $sd = new UnitqualitymanageModel();
            $flag = $sd->deleteTb($id);
            return json($flag);
        }
    }

}