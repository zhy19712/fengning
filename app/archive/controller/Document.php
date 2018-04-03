<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/28
 * Time: 16:02
 */

namespace app\archive\controller;

use app\admin\controller\Permissions;
use app\archive\model\DocumentAttachment;
use app\archive\model\DocumentDownRecord;
use app\archive\model\DocumentModel;
use app\archive\model\DocumentTypeModel;
use think\Db;
use think\Request;
use think\Session;

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
        $this->documentDownRecord = new DocumentDownRecord();
    }

    protected $documentService;
    protected $documentTypeService;
    protected $documentDownRecord;

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
     * 删除文档
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $par = input('id');

        $f = $this->documentService->deleteDoc($par);
        if ($f) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }

    /**
     * 共享文档
     * @return mixed
     */
    public function share()
    {
        return $this->fetch();
    }

    /**
     * 下载——权限验证
     * 部门下载部门内，若文件有权限设置则按规则
     */
    public function download()
    {
        $mod = DocumentModel::get(input('id'));
        //权限控制
        if (empty($mod['users'])) {

        } else if (!in_array(Session::get('current_id'), explode($mod['users'], "|"))) {
            return json(['code' => -2, 'msg' => "没有下载权限"]);
        }
        $file_obj = Db::name('attachment')->where('id', $mod['attachmentId'])->find();
        $filePath = '.' . $file_obj['filepath'];
        if (!file_exists($filePath)) {
            return json(['code' => '-1', 'msg' => '文件不存在']);
        } else if (request()->isAjax()) {
            return json(['code' => 1]); // 文件存在，告诉前台可以执行下载
        } else {
            //插入下载记录
            $this->documentDownRecord->save(['docId'=>$mod['id'],'user'=>Session::get('current_nickname')]);
            $fileName = $file_obj['filename'];
            $file = fopen($filePath, "r"); //   打开文件
            //输入文件标签
            $fileName = iconv("utf-8", "gb2312", $fileName);
            Header("Content-type:application/octet-stream ");
            Header("Accept-Ranges:bytes ");
            Header("Accept-Length:   " . filesize($filePath));
            Header("Content-Disposition:   attachment;   filename= " . $fileName);
            //   输出文件内容
            echo fread($file, filesize($filePath));
            fclose($file);
            exit;
        }
    }

    /**
     * 文档下载记录
     * @param $id 文档Id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function downloadrecord($id)
    {
        return json(DocumentDownRecord::all(['docId'=>$id]));
    }
}