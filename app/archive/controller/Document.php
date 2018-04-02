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
use app\archive\model\DocumentTypeModel;
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
        $this->documentTypeService = new DocumentTypeModel();
    }

    protected $documentService;
    protected $documentTypeService;

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
     * 属性
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getOne()
    {
        return json(
            DocumentModel::get(input('id'), ['documentType', 'attachmentInfo'])
        );
    }

    /**
     * 移动文档
     * @return \think\response\Json
     */
    public function move()
    {
        if ($this->request->isAjax()) {
            $mod = input('post.');
            if ($this->documentService->move($mod)) {
                return json(['code' => 1]);
            } else {
                return json(['code' => -1]);
            }
        }
        return $this->fetch();
    }

    /**
     * 编辑关键字
     * @return \think\response\Json
     */
    public function remark()
    {
        $mod = input('post.');
        if ($this->documentService->remark($mod)) {
            return json(['code' => 1]);
        }
        return json(['code' => -1]);
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
        $tids = $this->documentTypeService->getChilds($tid);
        $tids[] = $tid;
        if ($this->documentService->whereIn('type', $tids)->update(['status' => 1])) {
            return json(['code' => 1]);

        } else {
            return json(['code' => -1]);
        }
    }

    /**
     * 下载——权限验证
     * 部门下载部门内，若文件有权限设置则按规则
     */
    public function download()
    {
        return $this->redirect("");
    }
}