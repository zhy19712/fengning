<?php
/**
 * Created by PhpStorm.
 * User: waterforest
 * Date: 2018/3/22
 * Time: 10:09
 */

namespace app\admin\model;


use think\Model;

class DatatablesExample extends Model
{
    function datatablesExampleJoin(){
        return $this->hasOne('DatatablesExampleJoin','id');
    }

    function getAll(){
        return DatatablesExample::with('DatatablesExampleJoin')->select();
    }


}