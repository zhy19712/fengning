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
             * 前台需要传递的是 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             * 当新增 分项工程 的时候
             * 前台需要传递的是 pid 父级节点编号,d_code 编码,d_name 名称,type 分类,en_type 工程分类,primary 是否主要工程,remark 描述
             * 编辑 的时候 一定要 传递 id 编号
             *
             */
            $data = ['pid' => $param['pid'],'d_code' => $param['d_code'],'d_name' => $param['d_name'],'type' => $param['type'],'primary' => $param['primary'],'remark' => $param['remark']];
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


    public function importExcel(){
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/quality/division');
        if($info){
            // 调用插件PHPExcel把excel文件导入数据库
            Loader::import('PHPExcel\Classes\PHPExcel', EXTEND_PATH);
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads/quality/division' . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $excel_array= $obj_PHPExcel->getsheet(0)->toArray();   // 转换第一页为数组格式
            // 首先导入单位工程，根据单位工程设置根节点和二级节点
            $first_data['pid'] = 0;
            $first_data['name'] = $excel_array[0][0];
            // 验证格式 ---- 去除顶部菜单名称中的空格，并根据名称所在的位置确定对应列存储什么值
            $sn_index = $name_index = $primary_index = -1;
            foreach ($excel_array[1] as $k=>$v){
                $str = preg_replace('/[ ]/', '', $v);
                if($str == '编号'){
                    $sn_index = $k;
                }else if ($str == '单位工程名称'){
                    $name_index = $k;
                }else if ($str == '是否主控项目'){
                    $primary_index = $k;
                }
            }
            if($sn_index == -1 || $name_index == -1 || $primary_index == -1){
                $json_data['code'] = 0;
                $json_data['info'] = '首页的格式不对 - 01 (缺少[编号][单位工程名称][是否主控项目])';
                return json($json_data);
            }
            // 插入前首先判断是否是 重复插入
            $root_pid = Db::name('project_divide')->where('name',$first_data['name'])->value('id');
            if(!$root_pid){
                // 1,插入根节点
                $root_pid = Db::name('project_divide')->insertGetId($first_data); // 插入根节点
            }else{
                /**
                 * (危险操作) ==》文件已经上传并被使用过，这时如果重复导入excel那么之前关联的数据都会丢失!!!
                 * 虑到删除节点的影响，此处不允许重复上传
                 * (如果非要重复上传，除非此文件没被使用过，或者同意删除所有关联数据，然后联系管理员)
                 * (如果重复上传过于频繁，可以考虑新增对比和修改的操作)
                 */
                // 目前的解决方法是阻止重复上传
                return json(['code'=>0,'info'=>'警告:文件已存在。
                重复上传会删除所有关联的单元工程数据，如确认上传,请联系管理员']);

                // 获取二级子类 三级子类 四级子类
//                $second_subclass = $three_subclass = $four_subclass = [];
//                $second_subclass = Db::name('project_divide')->where('pid',$root_pid)->column('id');
//                if(!empty($second_subclass)){ // 为空验证避免误删一级节点
//                    $three_subclass = Db::name('project_divide')->whereIn('pid',$second_subclass)->column('id');
//                    if(!empty($three_subclass)){
//                        $four_subclass = Db::name('project_divide')->whereIn('pid',$three_subclass)->column('id');
//                    }
//                }
//                // 删除所有子类
//                $success = Db::name('project_divide')->delete($four_subclass);
//                $success = Db::name('project_divide')->delete($three_subclass);
//                $success = Db::name('project_divide')->delete($second_subclass);
            }
            /**
             * 2,批量插入二级节点
             * 如果是同一个文件上传，新上传的将会覆盖之前的。
             */
            $second_data = [];
            foreach($excel_array as $k=>$v){
                if($k > 1 && !empty($v[$sn_index]) && !empty($v[$name_index])){
                    $second_data[$k]['pid'] = $root_pid; // 根节点pid
                    $second_data[$k]['sn'] = $v[$sn_index]; // 单位工程编号
                    $second_data[$k]['name'] = $v[$name_index]; // 单位工程名称
                    $second_data[$k]['primary'] = $v[$primary_index]; // 是否主要单位工程
                }
            }
            $success = Db::name('project_divide')->insertAll($second_data);
            /**
             *  3,批量插入三级节点
             *  这里的页面 一般为 单位工程，分布工程，单元工程三大模块
             **/
            // 获取二级节点的自增编号做为三级节点的pid
            $second_list = Db::name('project_divide')->where('pid',$root_pid)->field('id,sn,primary')->select();
            $second_id_value = $second_id_array = $second_primary_array = [];
            foreach ($second_list as $pk=>$pv){
                $second_id_value[] = $pv['id']; // 二级节点的编号
                $second_id_array[$pv['id']] = $pv['sn']; // 将pid作为下标，单元工程编号sn作为值组成一个一维数组
                if(!empty($pv['primary'])){
                    $second_primary_array[$pv['sn']] = $pv['primary']; // 是否主要分部工程 继承上级
                }
            }
            $page_num = $obj_PHPExcel->getSheetCount(); // 获取excel一共有几页
            $three_data =[];
            for ($i=1;$i<$page_num;$i++){ // $i=1 第一页已经导入成功，这里从第二页开始导入
                $currentSheet = $obj_PHPExcel->getsheet($i)->toArray();   // 将每一页的数据转换为数组格式
                $current_pid = $current_primary = null;
                foreach ($currentSheet as $pk=>$page_v){ // 循环每一页的数组
                    if($pk > 2 && !empty($page_v[2]) && !empty($page_v[3])){ // 前几行都是标题
                        // 这里的in_array验证不能去掉，其目的是用来保留首次获取的上级编号
                        // 上级编号只写了一次，子类要继承pid这里就要做到当第一个单元格为空时，就继承第一次获取的pid
                        // 例如: P1-11	上库主坝	P1-11-1	 名称xxx
                        //                          P1-11-2	 名称xxx
                        //                          P1-11-3	 名称xxx
                        //      P1-12	下库主坝	P1-12-1	 名称xxx
                        //                          P1-12-2	 名称xxx
                        //                          P1-12-3	 名称xxx
                        if(in_array($page_v[0],$second_id_array)){
                            $current_pid =  array_search($page_v[0],$second_id_array);// 找到并返回键名做为上级pid
                            $current_primary =  array_key_exists($page_v[0],$second_primary_array) ? '是' : '';
                        }
                        // 如果pid不存在说明这里的第一个单元格的值为空或者不正确
                        if(!empty($page_v[0]) && !in_array($page_v[0],$second_id_array)){
                            $json_data['code'] = 0;
                            $json_data['info'] = '编号有误 -02 不存在的上级编号'.$page_v[0].'请检查第'.($i+1).'页第'.($pk+1).'行的首个单元格的编号';
                            return json($json_data);
                        }
                        $three_data[$i][$pk]['pid'] = $current_pid;
                        $three_data[$i][$pk]['sn'] = $page_v[2]; // 分部工程编号
                        $three_data[$i][$pk]['name'] = $page_v[3]; // 分部工程名称
                        $three_data[$i][$pk]['primary'] = $current_primary; // 是否主要分部工程 继承上级
                    }
                }
            }
            $three_new_data = [];
            foreach ($three_data as $k =>$v){
                foreach ($v as $v2){
                    if(!empty($v2['pid'])){
                        $three_new_data[] = $v2; // 将三维数组改为二维数组
                    }
                }
            }
            // 批量插入分部工程节点
            $success = Db::name('project_divide')->insertAll($three_new_data);
            /**
             * 4,批量插入四级节点
             */
            // 获取三级节点的自增编号做为四级节点的pid
            $three_list = Db::name('project_divide')->whereIn('pid',$second_id_value)->field('id,sn,primary')->select();
            $three_id_value = $three_id_array = $three_primary_array = [];
            foreach ($three_list as $pk=>$pv){
                $three_id_value[] = $pv['id'];
                // 将pid作为下标，单元工程编号sn作为值组成一个一维数组
                $three_id_array[$pv['id']] = $pv['sn'];
                if(!empty($pv['primary'])){
                    $three_primary_array[$pv['sn']] = $pv['primary'];
                }
            }
            $four_data = [];
            for ($i=1;$i<$page_num;$i++){ // $i=1 第一页已经导入成功，这里从第二页开始导入
                $currentSheet = $obj_PHPExcel->getsheet($i)->toArray();   // 将每一页的数据转换为数组格式
                $current_pid = $current_primary = null;
                foreach ($currentSheet as $pk=>$page_v){ // 循环每一页的数组
                    if($pk > 2 && !empty($page_v[4]) && !empty($page_v[5])){ // 前几行都是标题
                        if(in_array($page_v[2],$three_id_array)){
                            $current_pid =  array_search($page_v[2],$three_id_array);
                            $current_primary =  array_key_exists($page_v[2],$three_primary_array) ? '是' : '';
                        }
                        if(!empty($page_v[2]) && !in_array($page_v[2],$three_id_array)){
                            $json_data['status'] = 0;
                            $json_data['info'] = '编号有误 -03 不存在的上级编号'.$page_v[2].'请检查第'.($i+1).'页第'.($pk+1).'行的首个单元格的编号';
                            return json($json_data);
                        }
                        $four_data[$i][$pk]['pid'] = $current_pid; // 上级分布工程编号
                        $four_data[$i][$pk]['sn'] = $page_v[4]; // 单元工程编号
                        $four_data[$i][$pk]['name'] = $page_v[5]; // 单元工程名称
                        $four_data[$i][$pk]['primary'] = $current_primary; // 是否主要单元工程 继承上级
                        $four_data[$i][$pk]['job_content'] = $page_v[6]; // 单元工程 工作内容
                        $four_data[$i][$pk]['principle'] = $page_v[7]; // 单元工程 划分原则
                    }
                }
            }
            $four_new_data = [];
            foreach ($four_data as $k =>$v){
                foreach ($v as $v2){
                    if(!empty($v2['pid'])){
                        $four_new_data[] = $v2;
                    }
                }
            }
            // 批量插入单元工程节点
            $success = Db::name('project_divide')->insertAll($four_new_data);
            $json_data['code'] = 1;
            $json_data['info'] = '导入成功';
            return json($json_data);
        }else{
            $json_data['code'] = 0;
            $json_data['info'] = $file->getError();
            return json($json_data);
        }
    }


}