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
use app\quality\model\BranchfileModel;//分部质量管理文件上传
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\DivisionModel;//工程划分
use app\standard\model\ControlPoint;//控制点
use \think\Session;
use think\exception\PDOException;
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
     * 分部策划添加控制点
     * @return mixed
     */
    public function addPlan()
    {
        $param = input('param.');
        $selfid = $param["selfid"];
        $procedureid = $param["procedureid"];//工序号
        $this->assign('selfid', $selfid);
        $this->assign('procedureid', $procedureid);
        return $this->fetch();
    }

    /**
     * 分部策划 或者 分部管控 初始化左侧树节点
     * @param int $type
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function index($type = 1)
    {
        if($this->request->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo(4);
            return json($nodeStr);
        }
        if($type==1){
            return $this->fetch();
        }
        return $this->fetch('control');
    }

    /**
     * 获取分部质量管理-控制点
     * @return \think\response\Json
     */
    public function getControlPoint()
    {
        $data = Db::name('materialtrackingdivision')->group("id,name")->field("id,name")->where(['type'=>3,'cat'=>3])->select();
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
        if(request()->isAjax()){
            //实例化模型类
            $model = new BranchModel();
            //分部策划列表id
            $id = input('param.id');
            //查询一条分部策划列表中的信息
            $info = $model->getOne($id);
            if($info)
            {
                $controller_point_id = $info["controller_point_id"];//控制点id
                $flag = $model->associationDeletion($id,$controller_point_id);
                return json($flag);
            }
        }
    }

    /**
     * 全部删除控制点
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
    public function controlAllDel()
    {
        try{
            if(request()->isAjax()){
                //实例化模型类
                $model = new BranchModel();
                //分部策划列表id
                $procedureid = input('param.procedureid');//所属工序号
                //根据所属工序号查询所有的分部策划列表中的数据
                $data = $model->getAllid($procedureid);
                if(!empty($data))
                {
                    foreach ($data as $k=>$v)
                    {
                        //查询一条分部策划列表中的信息
                        $info = $model->getOne($v["id"]);
                        if($info)
                        {
                            $controller_point_id = $info["controller_point_id"];//控制点id
                            $model->associationDeletion($v['id'],$controller_point_id);
                        }
                    }
                    return ['code' => 1, 'msg' => '删除成功'];
                }
                else
                {
                    return ["code" => -1,"msg" => ""];
                }
            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /**
     * 添加控制点
     * @return \think\response\Json
     */
    public function addControlPoint()
    {
        try{

            if(request()->isAjax()){
                //实例化模型类
                $model = new BranchModel();
                $point = new ControlPoint();
                $param = input('post.');
                //前台传过来要添加的控制点数组，包含工程划分树的节点id，所属工序号procedureid，控制点id
                $selfid = $param["selfid"];//工程划分树的节点id
                $procedureid = $param["procedureid"];//所属工序号，对应的是materialtrackingdivision表中的id

                //需要的是一个二维数组
                $control_data = $param["control_id"];

                if(!empty($control_data))
                {
                    foreach ($control_data as $k=>$v)
                    {
                        //根据控制点id查询fengning_controlpoint表中的信息
                        $controlpoint_info = $point->getOne($v);
                        if(!empty($controlpoint_info))
                        {
                            $data = [
                                "selfid" => $selfid,
                                "procedureid" => $procedureid,//工序号
                                "controller_point_id" => $v,//控制点id
                                "controller_point_number" => $controlpoint_info["code"],//控制点编号
                                "controller_point_name" => $controlpoint_info["name"]//控制点名称
                            ];
                            $model->insertSu($data);
                        }
                    }
                    return ['code' => 1,'msg' => '添加成功'];
                }
                else
                {
                    return ['code' => -1,'msg' => ''];
                }
            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /**
     * 控制点执行情况文件 或者 图像资料文件上传
     * @return \think\response\Json
     */
    public function addFile()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new BranchfileModel();
            $branch = new BranchModel();
            $group = new AdminGroup();
            $admin = new Admin();
            $param = input('post.');

            $admin_id = Session::get('current_id');
            $admininfo = $admin->getadmininfo($admin_id);
            $group = $group->getOne($admininfo["admin_group_id"]);

            $data = [
                "list_id" => $param["list_id"],//分部策划列表id
                "file_image_name" => $param["file_image_name"],//上传的源文件名
                "attachment_id" => $param["attachment_id"],//对应的是attachment文件上传表中的id
                "owner" => Session::get('current_nickname'),//上传人
                "company" => $group["name"],//单位
                "admin_group_id" => $admininfo["admin_group_id"],//组织机构表中的id
                "date" => date("Y-m-d"),//上传时间，拍摄时间
                "type" => $param["type"]//1表示执行点执行情况，2表示图像资料

            ];
            $flag = $model->insertFile($data);

            //文件上传完毕后修改控制点的状态，只有上传控制点执行情况文件时才修改状态
            //先查询当前的状态
            if($param["type"] == 1)//1表示执行点执行情况
            {
                $info = $branch->getOne($param["list_id"]);
                if($info["status"] == 0)//0表示未执行
                {
                    $change = [
                        "id" => $param["list_id"],
                        "status" => "1"
                    ];
                    $branch->editSu($change);
                }
            }
            return json($flag);
        }
    }

    /**
     * 删除一条控制点执行情况或者是图像上传信息
     */
    public function delete()
    {
        if(request()->isAjax()){
            //实例化model类型
            $model = new BranchfileModel();
            $branch = new BranchModel();
            $param = input('post.');
                $data = $model->getOne($param['id']);
                if($data["attachment_id"])
                {
                    //先删除图片
                    //查询attachment表中的文件上传路径
                    $attachment = Db::name("attachment")->where("id",$data["attachment_id"])->find();
                    $path = "." .$attachment['filepath'];
                    $pdf_path = './uploads/temp/' . basename($path) . '.pdf';

                    if(file_exists($path)){
                        unlink($path); //删除文件图片
                    }

                    if(file_exists($pdf_path)){
                        unlink($pdf_path); //删除生成的预览pdf
                    }

                    //删除attachment表中对应的记录
                    Db::name('attachment')->where("id",$data["attachment_id"])->delete();
                }
                $flag = $model->delFile($param['id']);
                //只有执行点执行情况文件删除时进行以下的操作
                //如果控制点执行情况的文件全部删除，修改分部策划表中的状态到未执行，也就是0
                //首先查询控制点文件、图像上传表中是否还存在当前的分部策划表的上传文件记录
                if($param["type"] == 1)//1表示执行点执行情况
                {
                    $result = $model->judge($param["list_id"]);
                    if(empty($result))//为空为真表示已经没有文件,修改status的值
                    {
                        $info = $branch->getOne($param["list_id"]);
                        if($info["status"] == 1)//0表示已执行
                        {
                            $change = [
                                "id" => $param["list_id"],
                                "status" => "0"
                            ];
                            $branch->editSu($change);
                        }
                    }
                }
                return $flag;
        }
    }

    /**
     * 控制点执行情况文件 或者 图像资料文件 预览
     * @return \think\response\Json
     */
    public function preview()
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

    /**
     * 控制点执行情况文件 或者 图像资料文件 下载
     * @return \think\response\Json
     */
    public function download()
    {
        // 前台需要 传递 文件编号 id
        $param = input('param.');
        $file_id = isset($param['attachment_id']) ? $param['attachment_id'] : 0;
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



    /****************************分部管控************************/
    /**
     * 分部管控模板首页
     * @return mixed
     */
    public function control()
    {
        return $this->fetch();

    }

    /**
     * 分部管控添加控制点
     * @return mixed
     */
    public function addControl()
    {
        return $this->fetch();

    }
}