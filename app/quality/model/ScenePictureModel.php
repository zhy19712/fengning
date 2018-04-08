<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/8
 * Time: 9:22
 */
/**
 * 日常质量管理，图片管理
 * Class Atlas
 * @package app\quality\controller
 */
namespace app\quality\model;

use think\Model;
use think\exception\PDOException;

class ScenePictureModel extends Model
{
    protected $name='scene_picture';
    /*
     * 查询现场图片管理表中的所有的数据
     */

    public function getall()
    {
        return $this->field("id,name,pid")->select();
    }
}