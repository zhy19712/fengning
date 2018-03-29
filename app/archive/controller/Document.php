<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/28
 * Time: 16:02
 */

namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\DocumentModel;

/**
 * 文档管理
 * Class Document
 * @package app\archive\controller
 */
class Document extends Permissions
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 移动文档
     * @return \think\response\Json
     */
    public function move()
    {
        $mod = input('post.');
        $s = new DocumentModel();
        if ($s->move($mod)) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }
}