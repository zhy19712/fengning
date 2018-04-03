<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/29
 * Time: 9:23
 */

namespace app\archive\model;

use think\Model;

Class DocumentModel extends Model
{
    protected $name = 'archive_document';

    public function documentType()
    {
        return $this->hasOne("DocumentTypeModel", 'id', 'type');
    }

    public function attachmentInfo()
    {
        return $this->hasOne("DocumentAttachment", "id", "attachmentId");
    }

    /**
     * 新增
     * @param $mod
     * @return array
     */
    public function add($mod)
    {
        $res = $this->allowField(true)->save($mod);
        return $res;
    }

    /**
     * 移动文档
     * @param $parms
     * @return $this
     */
    public function move($parms)
    {
        return DocumentModel::update(['type' => $parms['type']], ['id' => $parms['id']]);
    }

    /**
     * 编辑关键字
     * @param $parms
     * @return $this
     */
    public function remark($parms)
    {
        return DocumentModel::update(['remark' => $parms['remark']], ['id' => $parms['id']]);
    }

    /**
     * 删除文档
     * @param $par
     * @return int
     * @throws \think\exception\DbException
     */
    public function deleteDoc($par)
    {
        $mod=self::get($par,'attachmentInfo');
        if(file_exists($mod['attachmentInfo']['filepatch'])){
            unlink($mod['attachmentInfo']['filepatch']); //删除上传的图片
        }
        return $mod->delete();
    }
}

Class DocumentAttachment extends Model
{
    protected $name = 'attachment';
}