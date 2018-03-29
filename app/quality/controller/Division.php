<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/27
 * Time: 16:16
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\DivisionModel;
use think\Db;
use think\Loader;
/**
 * 工程划分
 * Class Division
 * @package app\quality\controller
 */
class Division extends Permissions{

    /**
     * 初始化左侧树节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function index()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
        return $this->fetch();
    }

    /**
     * 点击编辑的时候
     * 获取一条节点信息
     * @return \think\response\Json
     * @author hutao
     */
    public function getNode()
    {
        if(request()->isAjax()){
            $param = input('post.');
            $id = isset($param['id']) ? $param['id'] : 0;
            $node = new DivisionModel();
            $info['node'] = $node->getOne($id);
            return json($info);
        }
    }

    /**
     * 新增 或者 编辑 工程划分的节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function editNode()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $param = input('post.');
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $en_type = isset($param['en_type']) ? $param['en_type'] : '';
            // 验证规则
            $rule = [
                ['section_id', 'require|number', '请选择标段|标段只能是数字'],
                ['pid', 'require|number', 'pid不能为空|pid只能是数字'],
                ['d_code', 'require|alphaDash', '编码不能为空|编码只能是字母、数字和破折号 - 的组合'],
                ['d_name', 'require|max:100', '名称不能为空|名称不能超过100个字符'],
                ['type', 'require|number', '请选择分类|分类只能是数字'],
                ['primary', 'require|number', '请选择是否是主要工程|是否是主要工程只能是数字']
            ];
            // 分类 1单位 2分部 3分项
            if($param['type'] != 3 && empty($en_type)){
                $validate = new \think\Validate($rule);
            }else{
                array_push($rule,['en_type', 'require|number', '请选择工程分类|工程类型只能是数字']);
                $validate = new \think\Validate($rule);
            }
            //验证部分数据合法性
            if (!$validate->check($param)) {
                $this->error('提交失败：' . $validate->getError());
            }

            /**
             * 节点 层级
             * 顶级节点 -》标段 -》单位工程 =》 子单位工程 =》分部工程 -》子分部工程 -》 分项工程 -》 单元工程 (注意 这是一条数据 是不在 树节点里的)
             *
             * 顶级节点 -》标段  不允许 增删改,它们是从其他表格获取的
             *
             * 当新增 单位工程 =》 子单位工程 =》分部工程 -》子分部工程 的时候
             * 前台需要传递的是 section_id 标段编号 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             * 当新增 分项工程 的时候
             * 前台需要传递的是 section_id 标段编号 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,en_type 工程分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             */
            $data = ['section_id' => $param['section_id'],'pid' => $param['pid'],'d_code' => $param['d_code'],'d_name' => $param['d_name'],'type' => $param['type'],'primary' => $param['primary'],'remark' => $param['remark']];
            if($param['type'] == 3 && !empty($en_type)){
                $data['en_type'] = $en_type;
            }
            if(empty($id)){
                $flag = $node->insertTb($data);
                return json($flag);
            }else{
                $data['id'] = $id;
                $flag = $node->editTb($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除 工程划分的节点
     * @return \think\response\Json
     * @author hutao
     */
    public function delNode()
    {
        /**
         * 前台只需要给我传递 要删除的 节点的 id 编号
         */
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id == 0){
            $node = new DivisionModel();
            // 是否包含子节点
            $exist = $node->isParent($id);
            if(!empty($exist)){
                return json(['code' => -1,'msg' => '包含子节点,不能删除']);
            }
            // 如果删除的是 分项工程 那么它 包含单元工程, 应该首先批量删除单元工程
            //Todo 如果 单元工程下面有 还包含其他的数据 那么 也要关联删除


            // 最后删除此节点
            $flag = $node->deleteTb($id);
            return json($flag);
        }else{
            return json(['code' => 0,'msg' => '编号有误']);
        }
    }

    /**
     * 获取路径
     * @return \think\response\Json
     * @author hutao
     */
    public function getParents()
    {
        /**
         * 前台就传递 当前点击的节点的 id 编号
         */
        if(request()->isAjax()){
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $node = new DivisionModel();
            $path = "";
            while($id>0)
            {
                $data = $node->getOne($id);
                $path = $data['name'] . ">>" . $path;
                $id = $data['pid'];
            }
            return json(['code' => 1,'path' => substr($path, 0 , -2),'msg' => "success"]);
        }
    }


    /**
     * 模板下载
     * @return \think\response\Json
     * @author hutao
     */
    public function excelDownload()
    {
        $filePath = '/uploads/工程划分导入模板.xlsx';
        if(!file_exists($filePath)){
            return json(['code' => '-1','msg' => '文件不存在']);
        }else if(request()->isAjax()){
            return json(['code' => 1]); // 文件存在，告诉前台可以执行下载
        }else{
            $fileName = '工程划分导入模板.xlsx';
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


    public function importExcel(){
        $section_id = input('param.$section_id');// 标段编号
        if(empty($section_id)){
            return  json(['code' => 0,'data' => '','msg' => '请选择标段']);
        }
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/quality/division/import');
        if($info){
            // 调用插件PHPExcel把excel文件导入数据库
            Loader::import('PHPExcel\Classes\PHPExcel', EXTEND_PATH);
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads/quality/division/import' . DS . $exclePath;   //上传文件的地址
            // 当文件后缀是xlsx 或者 csv 就会报：the filename xxx is not recognised as an OLE file错误
            $extension = substr(strrchr($file_name, '.'), 1);
            if ($extension =='xlsx') {
                $objReader = new \PHPExcel_Reader_Excel2007();
                $obj_PHPExcel = $objReader->load($file_name);
            } else if ($extension =='xls') {
                $objReader = new \PHPExcel_Reader_Excel5();
                $obj_PHPExcel = $objReader->load($file_name);
            } else if ($extension=='csv') {
                $PHPReader = new \PHPExcel_Reader_CSV();
                //默认输入字符集
                $PHPReader->setInputEncoding('GBK');
                //默认的分隔符
                $PHPReader->setDelimiter(',');
                //载入文件
                $obj_PHPExcel = $PHPReader->load($file_name);
            }else{
                return  json(['code' => 0,'data' => '','msg' => '请选择正确的模板文件']);
            }
            if(!is_object($obj_PHPExcel)){
                return  json(['code' => 0,'data' => '','msg' => '请选择正确的模板文件']);
            }
            $excel_array= $obj_PHPExcel->getsheet(0)->toArray();   // 转换第一页为数组格式
            // 验证格式 ---- 去除顶部菜单名称中的空格，并根据名称所在的位置确定对应列存储什么值
            $name_index = $code_index = -1;
            $z_name_index = $z_code_index = -1;
            $fname_index = $fcode_index = -1;
            $z_fname_index = $z_fcode_index = -1;
            $f_xname_index = $f_xcode_index = -1;
            foreach ($excel_array[0] as $k=>$v){
                $str = preg_replace('/[ ]/', '', $v);
                switch ($str){
                    case '单位工程名称':
                        $name_index = $k;
                        break;
                    case '单位工程编码':
                        $code_index = $k;
                        break;
                    case '子单位工程名称':
                        $z_name_index = $k;
                        break;
                    case '子单位工程编码':
                        $z_code_index = $k;
                        break;
                    case '分部工程名称':
                        $fname_index = $k;
                        break;
                    case '分部工程编码':
                        $fcode_index = $k;
                        break;
                    case '子分部工程名称':
                        $z_fname_index = $k;
                        break;
                    case '子分部工程编码':
                        $z_fcode_index = $k;
                        break;
                    case '分项工程名称':
                        $f_xname_index = $k;
                        break;
                    case '分项工程编码':
                        $f_xcode_index = $k;
                        break;
                    default :
                }
            }
            if($name_index == -1 || $code_index == -1 || $z_name_index == -1 || $z_code_index == -1 || $fname_index == -1 || $fcode_index == -1 || $z_fname_index == -1 || $z_fcode_index == -1 || $f_xname_index == -1 || $f_xcode_index == -1){
                return json(['code' => 0,'data' => '','msg' => '请检查标题名称']);
            }

            $insert_unit_data = []; // 单位工程
            $insert_subunit_data = []; // 子单位工程名称
            $insert_parcel_data = []; // 分部工程名称
            $insert_subdivision_data = []; // 子分部工程名称
            $insert_subitem_data = []; // 分项工程名称
            $new_excel_array = $this->delArrayNull($excel_array); // 删除空数据
            $path = './uploads/quality/division/import/' . str_replace("\\","/",$exclePath);
            foreach($new_excel_array as $k=>$v){
                if($k > 0){
                    // 单位工程
                    $insert_unit_data[$k]['name'] = $v[$name_index];
                    $insert_unit_data[$k]['code'] = $v[$code_index];
                    // 子单位工程名称
                    $insert_subunit_data[$k]['name'] = $v[$z_name_index];
                    $insert_subunit_data[$k]['code'] = $v[$z_code_index];
                    // 分部工程名称
                    $insert_parcel_data[$k]['name'] = $v[$fname_index];
                    $insert_parcel_data[$k]['code'] = $v[$fcode_index];
                    // 子分部工程名称
                    $insert_subdivision_data[$k]['name'] = $v[$z_fname_index];
                    $insert_subdivision_data[$k]['code'] = $v[$z_fcode_index];
                    // 分项工程名称
                    $insert_subitem_data[$k]['name'] = $v[$f_xname_index];
                    $insert_subitem_data[$k]['code'] = $v[$code_index];
                }
            }
            if($insert_unit_data){
                // 非表格数据
                $insert_unit_data[$k]['section_id'] = $section_id; // 标段编号
                $insert_unit_data[$k]['filepath'] = $path;
            }

            // 插入前首先判断是否是 重复插入
            $root_pid = Db::name('quality_division')->where(['d_code' => '','d_name' => ''])->value('id');
            if($root_pid){
                /**
                 * (危险操作) ==》文件已经上传并被使用过，这时如果重复导入excel那么之前关联的数据都会丢失!!!
                 * 虑到删除节点的影响，此处不允许重复上传
                 * (如果非要重复上传，除非此文件没被使用过，或者同意删除所有关联数据，然后联系管理员)
                 * (如果重复上传过于频繁，可以考虑新增对比和修改的操作)
                 */
                // 目前的解决方法是阻止重复上传
                return json(['code'=>0,'info'=>'警告:文件已存在.重复上传会删除所有关联的单元工程数据，如确认上传,请联系管理员']);
            }

            $success = Db::name('quality_division')->insertAll($insert_unit_data);
            if($success !== false){
                return  json(['code' => 1,'data' => '','msg' => '导入成功']);
            }else{
                return json(['code' => 0,'data' => '','msg' => '导入失败']);
            }
        }

    }

    /**
     * 清除数组中的空数组
     * @param $ar
     * @return mixed
     * @author hutao
     */
    public function delArrayNull($ar){
        foreach($ar as $k=>$v){
            $v = array_filter($v);
            if(empty($v) || is_null($v)){
                unset($ar[$k]);
            }
        }
        return $ar;
    }


}