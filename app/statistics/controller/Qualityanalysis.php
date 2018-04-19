<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/18
 * Time: 10:44
 */
/**
 * 质量管理-统计分析
 * Class Qualityanalysis
 * @package app\quality\controller
 */
namespace app\statistics\controller;
use app\admin\controller\Permissions;
use app\quality\model\DivisionModel;//工程划分
use \think\Session;
use think\exception\PDOException;
use think\Loader;
use think\Db;

class Qualityanalysis extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    public function getIndex()
    {
        //获取最小的时间
        $date = Db::name("quality_form_info")->where("create_time > 0")->min("create_time");
        //定义一个空的数组
        $data = array();
        $timeline = array();
        $excellentMonth = array();
        $rate = array();
        $StartMonth = date("Y-m-d",$date); //开始日期
        $EndMonth = date("Y-m-d"); //结束日期
        $ToStartMonth = strtotime( $StartMonth ); //转换一下
        $ToEndMonth   = strtotime( $EndMonth ); //一样转换一下
        $i            = false; //开始标示
        while( $ToStartMonth < $ToEndMonth ){
            $NewMonth = !$i ? date('Y-m', strtotime('+0 Month', $ToStartMonth)) : date('Y-m', strtotime('+1 Month', $ToStartMonth));
            $ToStartMonth = strtotime( $NewMonth );
            $i = true;
            $timeline[] = $NewMonth;//时间

            $excellentMonth[] = 50;//优良率

            $rate[] = array("excellent_number"=>70,"qualified_number"=>30);//优良单元数量，合格单元数量
        }


        $data = ["timeline"=>$timeline,"excellentMonth"=>$excellentMonth,"rate"=>$rate];

        return json(["code"=>1,"data"=>$data]);


    }

    public function section()
    {

    }

    public function getForm()
    {
        $data = array();
        $info = Db::name("quality_form_info")->field("form_data")->select();
        foreach ($info as $key=>$val) {
           $data[$key]= unserialize($val["form_data"]);
        }
        halt($data);
    }
}