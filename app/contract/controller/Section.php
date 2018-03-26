<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 16:13
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;
use app\contract\model\SectionModel;

class Section extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }
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