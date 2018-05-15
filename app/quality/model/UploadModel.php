<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/16
 * Time: 9:59
 */

namespace app\quality\model;

use think\Model;

class UploadModel extends Model
{
    protected $name = 'quality_upload';

    /**
     * 获取一条信息
     */
    public function getOne($id)
    {
        $data = $this->where('id', $id)->find();
        return $data;
    }
}

