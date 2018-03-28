<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/28
 * Time: 16:02
 */
namespace app\archive\controller;
use app\admin\controller\Permissions;

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
}