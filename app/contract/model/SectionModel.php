<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 16:14
 */

namespace app\contract\model;

use think\Model;

class SectionModel extends Model
{
    /**
     * 标段——新增或修改
     * @param $mod
     * @return array
     */
    public function AddOrEdit($mod)
    {
        if (empty($mod['id'])) {
            $res = SectionModel::allowField(true)->insert($mod);
        } else {
            $res = SectionModel::allowField(true)->save($mod, ['id' => $mod['id']]);
        }
        return $res ? true : false;
    }
}