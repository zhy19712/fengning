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
use app\quality\model\DivisionUnitModel;
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
     * GET 提交方式 :  获取一条节点数据
     * POST 提交方式 : 新增 或者 编辑 工程划分的节点
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function editNode()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $param = input('param.');
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $en_type = isset($param['en_type']) ? $param['en_type'] : '';
            if(request()->isGet()){
                $info['node'] = $node->getOne($id);
                // Todo 工程分类
                return json($info);
            }

            // 验证规则
            $rule = [
                ['section_id', 'require|number|gt:0', '请选择标段|标段只能是数字|请选择标段'],
                ['pid', 'require|number', 'pid不能为空|pid只能是数字'],
                ['d_code', 'require|alphaDash', '编码不能为空|编码只能是字母、数字、下划线 _和破折号 - 的组合'],
                ['d_name', 'require|max:100', '名称不能为空|名称不能超过100个字符'],
                ['type', 'require|number|gt:0', '请选择分类|分类只能是数字|请选择分类'],
                ['primary', 'number|egt:0', '请选择是否是主要工程|是否是主要工程只能是数字|请选择是否是主要工程']
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
                return json(['code' => -1,'msg' => $validate->getError()]);
            }

            /**
             * 节点 层级
             *
             * 顶级节点 -》标段  不允许 增删改,它们是从其他表格获取的
             *
             * 顶级节点 -》标段 下面 新增的是 -》单位工程 type = 1 , d_code 是自定义的
             *                                   单位工程 下面 新增的是 =》 子单位工程 type = 2 , d_code 前一部分 继承父级节点的编码,后面拼接自己的编码
             *                                                           =》 分部工程  type = 2 , d_code 前一部分 继承父级节点的编码,后面拼接自己的编码
             *                                                               分部工程 下面 新增的是 -》 子分部工程 type = 3 ,d_code 前一部分 继承父级节点的编码,后面拼接自己的编码
             *                                                                                      -》 分项工程   type = 3 ,d_code 前一部分 继承父级节点的编码,后面拼接自己的编码
             *                                                                                      -》 单元工程   type = 3 ,d_code 前一部分 继承父级节点的编码,后面拼接自己的编码
             *
             *                     注意:如果分部工程直接新建 -》 单元工程段号(单元划分) 的时候,  也必须 选择 工程分类
             *                               （ 这个可以前台判断 type= 2 时新增 单元工程段号(单元划分) 提示 请先给分部工程选择 工程分类)
             *                               ( 然后再在后台判断 type= 2 时新增 单元工程段号(单元划分) 提示 请先给分部工程选择 工程分类 或者 继续保存 分部工程的工程分类默认与当前单元工程段号(单元划分) 一致)
             *
             * 当新增 单位工程 , 子单位工程 ,分部工程  的时候
             * 前台需要传递的是 section_id 标段编号 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             * 当新增 子分部工程,分项工程 和 单元工程 的时候 必须 选择 工程分类
             * 前台需要传递的是 section_id 标段编号 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,en_type 工程分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             */
            $data = ['section_id' => $param['section_id'],'pid' => $param['pid'],'d_code' => $param['d_code'],'d_name' => $param['d_name'],'type' => $param['type'],'primary' => $param['primary'],'remark' => $param['remark']];
            if($param['type'] == 1){
                $data['pid'] = 0;
            }
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
        if($id != 0){
            $node = new DivisionModel();
            // 是否包含子节点
            $exist = $node->isParent($id);
            if(!empty($exist)){
                return json(['code' => -1,'msg' => '包含子节点,不能删除']);
            }
            // 如果删除的是 分项工程 那么它 有可能 包含单元工程, 应该首先批量删除单元工程
            // 分类 1单位 2分部 3分项
            $data = $node->getOne($id);
            if($data['type'] == 3){
                $unit = new DivisionUnitModel();
                $flag = $unit->batchDel($id);
                if($flag['code'] == -1){
                    return json($flag);
                }
            }

            //Todo 如果 单元工程下面 还包含其他的数据 那么 也要关联删除

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
            if($id == 0){ return json(['code' => 0,'path' => '','msg' => '编号有误']); }
            $node = new DivisionModel();
            $section_id = $path =  '';
            while($id>0)
            {
                $data = $node->getOne($id);
                $path = $data['d_name'] . ">>" . $path;
                $id = $data['pid'];
                $section_id = $data['section_id'];
            }
            $path = "丰宁抽水蓄能电站>>" .Db::name('section')->where('id',$section_id)->value('name') . ">>" . $path;
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
        $filePath = './static/division/division.xlsx';
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

    /**
     * 导入工程划分的节点
     * @return \think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author hutao
     */
    public function importExcel(){
        $section_id = input('param.section_id');// 标段编号
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
            $pid = $zpid = 0;
            foreach($new_excel_array as $k=>$v){
                if($k > 1){ // 前两行都是标题和说明
                    // 单位工程
                    $insert_unit_data['d_name'] = $v[$name_index];
                    $insert_unit_data['d_code'] = $v[$code_index];
                    $insert_unit_data['section_id'] = $section_id; // 标段编号
                    $insert_unit_data['filepath'] = $path;
                    $insert_unit_data['pid'] = '0';
                    $insert_unit_data['type'] = '1';
                    // 已经插入了，就不需要重复插入了
                    $flag = Db::name('quality_division')->where(['d_name'=>$v[$name_index],'d_code'=>$v[$code_index],'section_id'=>$section_id,'type'=>'1'])->find();
                    if(empty($flag) && !empty($v[$name_index])){
                        $node = new DivisionModel();
                        $flag = $node->insertTb($insert_unit_data);
                        if($flag['code'] == -1){
                            return json(['code' => 0,'data' => '','msg' => '单位工程-导入失败']);
                        }
                        $pid = $flag['data']['id'];
                    }else{
                        $pid = $flag['id'];
                    }

                    // 子单位工程名称
                    $insert_subunit_data['d_name'] = $v[$z_name_index];
                    $insert_subunit_data['d_code'] = $v[$z_code_index];
                    $insert_subunit_data['section_id'] = $section_id; // 标段编号
                    $insert_subunit_data['filepath'] = $path;
                    $insert_subunit_data['type'] = '2';
                    // 已经插入了，就不需要重复插入了
                    $flag = Db::name('quality_division')->where(['d_name'=>$v[$z_name_index],'d_code'=>$v[$z_code_index],'section_id'=>$section_id,'type'=>'2'])->find();
                    if(empty($flag) && !empty($v[$z_name_index])){
                        $insert_subunit_data['pid'] = $pid;
                        $node = new DivisionModel();
                        $flag = $node->insertTb($insert_subunit_data);
                        if($flag['code'] == -1){
                            return json(['code' => 0,'data' => '','msg' => '子单位工程-导入失败']);
                        }
                        $zpid = $flag['data']['id'];
                    }else{
                        $zpid = $flag['id'];
                    }

                    // 分部工程名称
                    $insert_parcel_data['d_name'] = $v[$fname_index];
                    $insert_parcel_data['d_code'] = $v[$fcode_index];
                    $insert_parcel_data['section_id'] = $section_id; // 标段编号
                    $insert_parcel_data['filepath'] = $path;
                    $insert_parcel_data['type'] = '2';
                    // 已经插入了，就不需要重复插入了
                    $flag = Db::name('quality_division')->where(['d_name'=>$v[$fname_index],'d_code'=>$v[$fcode_index],'section_id'=>$section_id,'type'=>'2'])->find();
                    if(empty($flag) && !empty($v[$fname_index])){
                        $insert_parcel_data['pid'] = $pid;
                        $node = new DivisionModel();
                        $flag = $node->insertTb($insert_parcel_data);
                        if($flag['code'] == -1){
                            return json(['code' => 0,'data' => '','msg' => '分部工程-导入失败']);
                        }
                        $zpid = $flag['data']['id'];
                    }else{
                        $zpid = $flag['id'];
                    }

                    // 子分部工程名称
                    $insert_subdivision_data['d_name'] = $v[$z_fname_index];
                    $insert_subdivision_data['d_code'] = $v[$z_fcode_index];
                    $insert_subdivision_data['section_id'] = $section_id; // 标段编号
                    $insert_subdivision_data['filepath'] = $path;
                    $insert_subdivision_data['type'] = '3';
                    // 已经插入了，就不需要重复插入了
                    $flag = Db::name('quality_division')->where(['d_name'=>$v[$z_fname_index],'d_code'=>$v[$z_fcode_index],'section_id'=>$section_id,'type'=>'3'])->find();
                    if(empty($flag) && !empty($v[$z_fname_index])){
                        $insert_subdivision_data['pid'] = $zpid;
                        $node = new DivisionModel();
                        $flag = $node->insertTb($insert_subdivision_data);
                        if($flag['code'] == -1){
                            return json(['code' => 0,'data' => '','msg' => '子分部工程-导入失败']);
                        }
                    }
                    // 分项工程名称
                    $insert_subitem_data['d_name'] = $v[$f_xname_index];
                    $insert_subitem_data['d_code'] = $v[$f_xcode_index];
                    $insert_subitem_data['section_id'] = $section_id; // 标段编号
                    $insert_subitem_data['filepath'] = $path;
                    $insert_subitem_data['type'] = '3';
                    // 已经插入了，就不需要重复插入了
                    $flag = Db::name('quality_division')->where(['d_name'=>$v[$f_xname_index],'d_code'=>$v[$f_xcode_index],'section_id'=>$section_id,'type'=>'3'])->find();
                    if(empty($flag) && !empty($v[$f_xname_index])){
                        $insert_subitem_data['pid'] = $zpid;
                        $node = new DivisionModel();
                        $flag = $node->insertTb($insert_subitem_data);
                        if($flag['code'] == -1){
                            return json(['code' => 0,'data' => '','msg' => '分项工程-导入失败']);
                        }
                    }
                }
            }

            return  json(['code' => 1,'data' => '','msg' => '导入成功']);
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

    /**
     * GET 提交方式 :  获取一条 单元工程数据
     * POST 提交方式 : 新增 或者 编辑 单元工程
     * @return \think\response\Json
     * @author hutao
     */
    public function editUnit()
    {
        if(request()->isAjax()){
            $unit = new DivisionUnitModel();
            $param = input('param.');
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if(request()->isGet()){
                $info['node'] = $unit->getOne($id);
                // Todo 施工依据
                // Todo 工程类型
                return json($info);
            }

            // 验证规则
            $rule = [
                ['division_id', 'require|number|gt:0', '请选择分项工程|分项工程编号只能是数字|请选择分项工程'],
                ['serial_number', 'require|alphaDash', '单元工程流水号不能为空|单元工程流水号只能是字母、数字、下划线 _和破折号 - 的组合'],
                ['site', 'require|max:100', '单元工程部位不能为空|单元工程部位不能超过100个字符'],
                ['coding', 'require|alphaDash', '系统编码不能为空|系统编码只能是字母、数字、下划线 _和破折号 - 的组合'],
                ['ma_bases', 'require', '请选择施工依据'],
                ['hinge', 'require|number', '请选择是否是关键部位|关键部位只能是数字'],
                ['en_type', 'require|number|gt:0', '请选择工程类型|工程类型只能是数字|请选择工程类型'],
                ['start_date', 'date', '开工日期格式有误']
            ];
            $validate = new \think\Validate($rule);
            //验证部分数据合法性
            if (!$validate->check($param)) {
                return json(['code' => -1,'msg' => $validate->getError()]);
            }

            /**
             * type= 2 时新增 单元工程段号(单元划分)
             * 提示 请先给分部工程选择 工程分类 或者 继续保存 分部工程的工程分类默认与当前单元工程段号(单元划分) 一致
             */
            $type = Db::name('quality_division')->where('id',$param['division_id'])->value('type');
            $again_save = isset($param['again_save']) ? $param['again_save'] : '';
            if($type == 2 && $again_save != 'again_save'){
                return json(['code' => -1,'msg' => '继续保存： 分部工程的工程分类默认与当前单元工程段号(单元划分) 一致']);
            }

            /**
             * 单元工程 (注意 这是归属于分项工程下的 数据信息 它不是节点)
             *
             * 当新增 单元工程 的时候
             * 前台需要传递的是
             * 必传参数 : division_id 归属的分项工程编号 serial_number 单元工程流水号,
             *            site 单元工程部位,coding 系统编码,ma_bases 施工依据(注意这里是可以多选的，选中的编号以下划线连接 例如：1_2_3_4_5 ),
             *            hinge 关键部位(1 是 0 否),en_type 工程类型
             *
             * 可选参数 : su_basis 补充依据,el_start 高程（起）,el_cease 高程（止）,quantities 工程量,pile_number 起止桩号,start_date 开工日期,completion_date 完工日期
             *
             * 编辑 的时候 一定要 传递 id 编号
             *
             */
            if(empty($id)){
                $flag = $unit->insertTb($param);
                return json($flag);
            }else{
                $flag = $unit->editTb($param);
                return json($flag);
            }
        }
    }


    /**
     * 删除 单元工程
     * @return \think\response\Json
     * @author hutao
     */
    protected function delUnit()
    {
        /**
         * 前台只需要给我传递 要删除的 单元工程的 id 编号
         */
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if($id != 0){
            $unit = new DivisionUnitModel();
            $flag = $unit->deleteTb($id);
            return json($flag);
        }else{
            return json(['code' => 0,'msg' => '编号有误']);
        }
    }

}