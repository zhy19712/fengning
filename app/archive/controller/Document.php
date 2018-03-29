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
use think\Request;

/**
 * 文档管理
 * Class Document
 * @package app\archive\controller
 */
class Document extends Permissions
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->documentService = new DocumentModel();
    }

    protected $documentService;

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        $mod = input('post.');
        if ($this->documentService->add($mod)) {
            return ['code' => 1];
        } else {
            return ['code' => -1];
        }
    }

    /**
     * 移动文档
     * @return \think\response\Json
     */
    public function move()
    {
        $mod = input('post.');
        if ($this->documentService->move($mod)) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }

    /**
     * 归档
     * @return \think\response\Json
     */
    public function archiving()
    {
        $id = input('id');
        if (
        DocumentModel::update(['status' => 1], ['id' => $id])) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }

    /**
     * 一键归档
     * @return \think\response\Json
     */
    public function batchArchiving()
    {
        $tid = input('tid');
        if (DocumentModel::update(['status' => 1], ['type' => $tid])) {
            return json(['code' => 1]);

        } else {
            return json(['code' => -1]);
        }
    }
}