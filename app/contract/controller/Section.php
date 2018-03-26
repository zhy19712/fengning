<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 16:13
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;
use app\admin\model\AdminCate;
use app\contract\model\SectionModel;

class Section extends Permissions
{

    public function index()
    {
        return $this->fetch();
    }
    public function add()
    {
//        $orgs=AdminCate::
        $this->assign('orgs',json_encode());
        return $this->fetch();
    }

    /**
     * 标段——新增或修改
     * @return array
     */
    public function addoredit()
    {
        if ($this->request->isAjax()) {
            try {
                $mod = input('post');
                $m = new SectionModel();
                $res = $m->AddOrEdit($mod);
                if ($res) {
                    return ['code' => 1];
                } else {
                    return ['code' => -1];
                }
            } catch (Exception $e) {
                return ['code' => -1, 'msg' => $e->getMessage()];
            }
        }
    }

    /**
     * 标段——删除
     * @return array
     */
    public function del()
    {
        if ($this->request->isAjax()) {
            try {
                $id = input('id');
                $res = SectionModel::destroy($id);
                if ($res) {
                    return ['code' => 1];
                } else {
                    return ['code' => -1];
                }
            } catch (Exception $e) {
                return ['code' => -1, 'msg' => $e->getMessage()];
            }
        }
    }
}