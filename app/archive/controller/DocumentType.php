<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/29
 * Time: 13:51
 */

namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\DocumentTypeModel;

class DocumentType extends Permissions
{
    /**
     * 获取树
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getAll()
    {
        return json(DocumentTypeModel::all());
    }

    /**
     * 添加或修改
     * @return \think\response\Json
     */
    public function addOrEdit()
    {
        $m=input('psot.');
        $s=new DocumentTypeModel();
        if ($s->addOrEdit($m))
        {
            return json(['code'=>1]);
        }else{
            return json(['code'=>-1]);
        }
    }
}