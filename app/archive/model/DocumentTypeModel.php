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
    protected $name='archive_documenttype';

    public function addOrEdit($mod)
    {
        if (empty($mod['id'])) {
            $res = $this->allowField(true)->save($mod);
        } else {
            $res= $this->allowField(true)->save($mod, ['id' => $mod['id']]);
        }
        return $res?true:false;
    }
}