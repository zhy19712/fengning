<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/11
 * Time: 14:19
 */

namespace app\quality\model;


use think\Db;
use think\exception\PDOException;
use think\Model;

class UnitqualitymanageModel extends Model
{
    protected $name = 'quality_division_controlpoint_relation';

    /**
     * @param $add_id
     * @param $ma_division_id
     * @param $id
     * @return array
     * @throws \think\Exception
     * @author hutao
     */
    public function associationDeletion($add_id,$ma_division_id,$id)
    {
        try{
            /**
             * 控制点 存在于 controlpoint 表 和 quality_division_controlpoint_relation 中
             * controlpoint 表里的数据 是原始的，quality_division_controlpoint_relation 是 在 单位策划里 后来 新增的关系记录
             *
             * 如果关系记录存在 该控制点 那么就应该先
             * 要关联 删除 记录里的控制点执行情况 和 图像资料  以及它们所包含的文件 以及 预览的pdf文件
             * 然后 删除 这条关系记录
             *
             * 最后 删除 原始数据
             *
             * type 类型:1 检验批 0 工程划分
             */
            if($id == 0){ // 全部删除
                $relation_id = $this->where(['division_id'=>$add_id,'ma_division_id'=>$ma_division_id,'type'=>0])->column('id');
            }else{
                $relation_id = $this->where(['division_id'=>$add_id,'ma_division_id'=>$ma_division_id,'control_id'=>$id,'type'=>0])->value('id');
            }
            if(is_array($relation_id) && sizeof($relation_id)){
                $data = Db::name('quality_upload')->where('contr_relation_id',$relation_id)->column('id,attachment_id');
                if(is_array($data)){
                    $id_arr = array_keys($data);
                    $attachment_id_arr = array_values($data);
                    $att = Db::name('attachment')->whereIn('id',$attachment_id_arr)->column('filepath');
                    foreach ($att as $v){
                        $pdf_path = './uploads/temp/' . basename($v) . '.pdf';
                        if(file_exists($v)){
                            unlink($v); //删除文件
                        }
                        if(file_exists($pdf_path)){
                            unlink($pdf_path); //删除生成的预览pdf
                        }
                    }
                    Db::name('attachment')->delete('attachment_id_arr');
                    $this->delete($id_arr);
                }
            }else{
                $data = Db::name('quality_upload')->where('contr_relation_id',$relation_id)->column('id,attachment_id');
                if(is_array($data)){
                    $id_arr = array_keys($data);
                    $attachment_id_arr = array_values($data);
                    $att = Db::name('attachment')->whereIn('id',$attachment_id_arr)->column('filepath');
                    foreach ($att as $v){
                        $pdf_path = './uploads/temp/' . basename($v) . '.pdf';
                        if(file_exists($v)){
                            unlink($v); //删除文件
                        }
                        if(file_exists($pdf_path)){
                            unlink($pdf_path); //删除生成的预览pdf
                        }
                    }
                    Db::name('attachment')->delete('attachment_id_arr');
                    $this->delete($id_arr);
                }
            }
            Db::name('controlpoint')->delete($id);
            return ['code' => 1, 'msg' => '删除成功'];
        }catch(PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

}