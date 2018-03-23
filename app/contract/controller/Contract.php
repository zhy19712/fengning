<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 13:52
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;
use app\contract\model\ContractModel;
use think\Exception;
use think\Request;

class Contract extends Permissions

{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 新增或修改
     * @return array
     */
    public function addoredit()
    {
        if ($this->request->isAjax()) {
            try {
                $mod = input('post');
                $m = new ContractModel();
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
                $res = ContractModel::destroy($id);
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