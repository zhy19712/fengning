<?php
//主页，控制面板

namespace app\admin\controller;

use app\quality\model\CustomAttributeModel;
use \think\Db;
use \think\Cookie;
use app\admin\controller\Permissions;

class Main extends Permissions
{
    public function index()
    {

        return $this->fetch();
    }

    public function selectperson()
    {
        return $this->fetch();
    }

    public function addAttr()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $attrKey = isset($param['attrKey']) ? $param['attrKey'] : -1;
            $attrKey = isset($param['attrKey']) ? $param['attrKey'] : -1;
            $picture = new CustomAttributeModel();

        }
    }

    public function addRemark()
    {

    }

}
