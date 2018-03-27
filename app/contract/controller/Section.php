<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 16:13
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;
use app\contract\model\SectionModel;
use think\Db;

class Section extends Permissions
{

    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        $orgs = AdminGroup::all(['category' => 1]);
        $this->assign('orgs', json_encode($orgs));
        return $this->fetch();
    }

    public function sections()
    {
        $m=Db::name('section')->alias('a')
            ->join('admin_group b','a.builderId=b.id','left')
            ->join('admin_group c','a.constructorId=c.id','left')
            ->join('admin_group d','a.designerId=d.id','left')
            ->join('admin_group e','a.supervisorId=e.id','left')
            ->field('a.id,a.name,a.money,b.name as builder,c.name as constructor,d.name as designer,e.name as supervisor')
            ->select();
//        $m=SectionModel::get(1)->with('builder');
//        $m=Db::name("section")->with("builder") ->select();
        return json($m);
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