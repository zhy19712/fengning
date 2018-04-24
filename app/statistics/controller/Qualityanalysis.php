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

    /**
     * 统计数据的柱状图、表格、折线图
     * @return \think\response\Json
     */
    public function getIndex()
    {
        //折线
        //获取最小的时间
        $date = Db::name("quality_form_info")->where("create_time > 0")->min("create_time");
        //定义一个空的数组
        $timeline = array();
        $excellentMonth = array();
        $rate = array();
        $section_rate = array();
        $total = array();
        $total_number = array();
        $info = array();
        $info_data = array();
        $result_data = array();
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
            $section = Db::name("section")->column("id");//标段名
            foreach($section as $key=>$val)
            {
                $info[] = Db::name('quality_form_info')->alias('r')
                    ->join('quality_unit u', 'u.id = r.DivisionId', 'left')
                    ->join('quality_division c','c.id=u.division_id','left')
                    ->join('section s', 'c.section_id = s.id', 'left')
                    ->where('s.id',$val)->field("r.form_data")->select();
            }

            $excellentMonth[] = 50;//优良率

            $rate[] = array("excellent_number"=>70,"qualified_number"=>20);//优良单元数量，合格单元数量
        }

        foreach($info as $ke=>$va)
        {
            foreach ($va as $kee=>$vaa)
            {
                $info_data[]=(unserialize($vaa["form_data"]));
            }
        }


        foreach($info_data as $a=>$b)
        {
            $result_data[]=($b[count($b)-9]);
        }




        //柱状图
        $section = Db::name("section")->column("name");//标段名
        foreach($section as $k=>$v)
        {
            $section_rate_number[] = array("excellent_number"=>70,"qualified_number"=>20);//优良单元数量，合格单元数量

            $total_number[] = 100;//总量

            $section_rate[] = array("excellent_number"=>70,"qualified_number"=>20);//优良率，合格率

            $total[] = 70+20;//总计 = 优良率+合格率
        }

        $result = ["section"=>$section,"section_rate_number"=>$section_rate_number,"total_number"=>$total_number,"section_rate"=>$section_rate,"total"=>$total];
        $data = ["timeline"=>$timeline,"excellentMonth"=>$excellentMonth,"rate"=>$rate];

        return json(["code"=>1,"data"=>$data,"result"=>$result]);


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

    public function test()
    {

    }

    function one($n){
        $array = array();
        $array[0] = 1;
        $array[1] = 1;
        for($i=2;$i<$n;$i++){
            $array[$i] = $array[$i-1]+$array[$i-2];
        }
        print_r($array);
    }



}