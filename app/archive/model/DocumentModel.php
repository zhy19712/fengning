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
        return $this->hasOne("DocumentAttachment","id","attachmentId");
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
}
Class DocumentAttachment extends Model{
    protected $name='attachment';
}