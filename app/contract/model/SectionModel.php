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
    protected $table='fengnig_section';
    /**
     * 外键——合同
     * @return \think\model\relation\HasOne
     */
    public function contract()
    {
        return $this->hasOne('ContractModel', 'id', 'contractId');
    }

    /**
     * 外键——业主现场管理机构
     * @return \think\model\relation\HasOne
     */
    public function builder()
    {
        return $this->hasOne('AdminGroup', 'id', 'builderId');
    }
    /**
     * 外键——监理现场管理机构
     * @return \think\model\relation\HasOne
     */
    public function supervisor()
    {
        return $this->hasOne('AdminGroup', 'id', 'supervisorId');
    }
    /**
     * 外键——施工现场管理机构
     * @return \think\model\relation\HasOne
     */
    public function constructor()
    {
        return $this->hasOne('AdminGroup', 'id', 'constructorId');
    }
    /**
     * 外键——设计现场管理机构
     * @return \think\model\relation\HasOne
     */
    public function designer()
    {
        return $this->hasOne('AdminGroup', 'id', 'designerId');
    }
    /**
     * 外键——其他现场管理机构
     * @return \think\model\relation\HasOne
     */
    public function otherId()
    {
        return $this->hasOne('AdminGroup', 'id', 'otherId');
    }
    /**
     * 外键——验评用户
     * @return \think\model\relation\HasOne
     */
    public function eveluateUser()
    {
        return $this->hasOne('Admin', 'id', 'eveluateUserId');
    }

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