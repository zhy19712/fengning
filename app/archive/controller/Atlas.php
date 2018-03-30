<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 9:19
 */
namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\AtlasCateTypeModel;//左侧节点树
use app\archive\model\AtlasCateModel;//右侧图册类型
use \think\Db;
use \think\Session;
/**
 * 图纸文档管理，图册管理
 * Class Atlas
 * @package app\archive\controller
 */
class Atlas extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }
    /**********************************左侧图册类型树************************/
    /**
     * 图册分类树
     * @return mixed|\think\response\Json
     */
    public function atlastree()
    {
        // 获取左侧的树结构
        if(request()->isAjax()){
            $node = new AtlasCateTypeModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
        return $this->fetch();
    }

    /**
     * 新增 或者 编辑 图册类型的节点树
     * @return mixed|\think\response\Json
     */
    public function editCatetype()
    {
        if(request()->isAjax()){
            $model = new AtlasCateTypeModel();
            $param = input('post.');
            /**
             * 前台需要传递的是 pid 父级节点编号,id图册类型树自增,name图册节点树分类名
             */
            if(empty($param['id']))//id为空时表示新增图册类型节点
            {
                $data = [
                    'pid' => $param['pid'],
                    'name' => $param['name']
                ];
                $flag = $model->insertCatetype($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'name' => $param['name']
                ];
                $flag = $model->editCatetype($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除图册类型的节点树
     * @return \think\response\Json
     */
    public function delCatetype()
    {
        if(request()->isAjax()){
            //实例化图册类型AtlasCateTypeModel
            $model = new AtlasCateTypeModel();
            $param = input('post.');
            //删除图册分类


            //删除上传的图片


            //删除图册类型树节点
            $flag = $model->delCatetype($param['id']);
            return json($flag);
        }else
        {
            return $this->fetch();
        }

    }

    /**
     * 上移下移
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function sortNode()
    {
        if(request()->isAjax()){
            try {

                $change_id = $this->request->has('change_id') ? $this->request->param('change_id', 0, 'intval') : 0; //影响节点id,包括上移下移,没有默认0
                $change_sort_id = $this->request->has('change_sort_id') ? $this->request->param('change_sort_id', 0, 'intval') : 0; //影响节点的排序编号sort_id 没有默认0

                $select_id = input('post.select_id'); // 当前节点的编号
                $select_sort_id = input('post.select_sort_id'); // 当前节点的排序编号

                Db::name('atlas_cate_type')->where('id', $select_id)->update(['sort_id' => $change_sort_id]);
                Db::name('atlas_cate_type')->where('id', $change_id)->update(['sort_id' => $select_sort_id]);

                return json(['code' => 1,'msg' => '移动成功']);

            }catch (PDOException $e){
                return ['code' => -1,'msg' => $e->getMessage()];
            }


        }
        return $this->fetch();
    }
    /**********************************右侧图册表************************/
    /**
     * 新增/编辑图册
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function editAtlasCate()
    {
        if(request()->isAjax()){
            $model = new AtlasCateModel();
            $param = input('post.');
            $param["selfid"] = 11;
            //首先查询选择的图册节点树的id，在此分类下的最大的序号cate_number
            //然后在此基础上自动加1
            $max_cate_number = $model->maxcatenumber($param['selfid']);

            //前台传过来的角色类型id
            if(empty($param['id']))//id为空时表示新增图册类型
            {
                $data = [
                    'selfid' => $param['selfid'],//admin_cate_type表中的id,区分图册节点树
                    'cate_number' => $max_cate_number + 1,//序号
                    'picture_number' => $param['picture_number'],//图号
                    'picture_name' => $param['picture_name'],//图名
                    'picture_papaer_num' => $param['picture_papaer_num'],//图纸张数(输入数字)
                    'a1_picture' => $param['a1_picture'],//折合A1图纸
                    'design_name' => $param['design_name'],//设计
                    'check_name' => $param['check_name'],//校验
                    'examination_name' => $param['examination_name'],//审查
                    'completion_date' => $param['completion_date'],//完成日期
                    'section' => $param['section'],//标段
                    'paper_category' => $param['paper_category'],//图纸类别
                    'owner' => Session::get('current_nickname'),//上传人
                    'date' => date("Y-m-d H:i:s")//上传日期
                ];
                $flag = $model->insertCate($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],//图册类型表自增id
//                    'selfid' => $param['selfid'],//admin_cate_type表中的id,区分图册节点树
//                    'cate_number' => $max_cate_number + 1,//序号
                    'picture_number' => $param['picture_number'],//图号
                    'picture_name' => $param['picture_name'],//图名
                    'picture_papaer_num' => $param['picture_papaer_num'],//图纸张数(输入数字)
                    'a1_picture' => $param['a1_picture'],//折合A1图纸
                    'design_name' => $param['design_name'],//设计
                    'check_name' => $param['check_name'],//校验
                    'examination_name' => $param['examination_name'],//审查
                    'completion_date' => $param['completion_date'],//完成日期
                    'section' => $param['section'],//标段
                    'paper_category' => $param['paper_category']//图纸类别
//                    'owner' => Session::get('current_nickname'),//上传人
//                    'date' => date("Y-m-d H:i:s")//上传日期
                ];
                $flag = $model->editCate($data);
                return json($flag);
            }
        }

    }

}