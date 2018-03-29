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
    function documentType()
    {
        return $this->hasOne("DocumentType",'id','type');
    }
}