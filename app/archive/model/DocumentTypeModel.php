<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/29
 * Time: 9:32
 */
namespace app\archive\model;
use think\Model;

class DocumentTypeModel extends Model{
    protected $name='archive_document_type';

    public function addOrEdit()
    {
        if (empty($mod['id'])) {
            $res = $this->allowField(true)->insert($mod);
        } else {
            $res= $this->allowField(true)->save($mod, ['id' => $mod['id']]);
        }
        return $res?true:false;
    }
    /**
     * 删除
     * @param $id
     * @return int
     */
    public function del($id)
    {
        return DocumentTypeModel::destroy($id);
    }

    public function getAll()
    {
        return DocumentTypeModel::all();
    }
}