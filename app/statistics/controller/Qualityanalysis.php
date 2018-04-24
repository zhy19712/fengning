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
        $info = array();
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

        }

       array_pop($timeline);

        foreach($timeline as $keee=>$vaaa)
        {

                //开始日期
                $start = mktime(0,0,0,date("m",strtotime($vaaa)),1,date("Y",strtotime($vaaa)));

                //结束日期
                $end = mktime(23,59,59,date('m',strtotime($vaaa)),date('t'),date('Y',strtotime($vaaa)));

                $temp_info_data= Db::name('quality_form_info')->field("form_data,id,create_time")->where("form_name like '%等级评定表%'")->where("create_time >= ".$start. " AND create_time <= ".$end)->select();
                if($temp_info_data)
                {
                    $info[] = $temp_info_data;
                }else
                {
                    $info[] = [];
                }

        }

        foreach($info as $ke=>$va)
        {
            foreach ($va as $kee=>$vaa)
            {
                $info[$ke][$kee]=(unserialize($vaa["form_data"]));
            }
        }

        foreach($info as $a=>$b)
        {
            foreach($b as $c=>$d)
            {
                $info[$a][$c] = ($d[count($d)-9]);
            }

        }

        foreach($info as $l=>$m)
        {
            foreach($m as $x=>$y)
            {
               if($y["Step"] != 3)
               {
                   unset($info[$l][$x]);
               }
            }
        }

        //定义一个空的数组
        $form_result = array();
        $count_number = array();
        foreach ($info as $o=>$p)
        {
            $count = array_count_values(array_column($p,"Value"));//统计优良、合格、不合格的数量
            if(isset($count["优良"]) && isset($count["合格"]))
            {
                $count_number["excellent_number"] = $count["优良"];
                $count_number["qualified_number"] = $count["合格"];
                $form_result[$o]['count'] = $count_number;
                $count["优良"] = $count["优良"]?$count["优良"]:0;
                $count["合格"] = $count["合格"]?$count["合格"]:0;
                //计算优良率，合格率不合格率
                $form_result[$o]['excellent'] = round($count["优良"]/($count["优良"] + $count["合格"]) * 100);//优良率
                $form_result[$o]['qualified'] = round($count["合格"]/($count["优良"] + $count["合格"]) * 100);//合格率
            }else if(!isset($count["优良"]) && isset($count["合格"]))
            {
                $count_number["excellent_number"] = 0;
                $count_number["qualified_number"] = $count["合格"];
                $form_result[$o]['count'] = $count_number;
                $count["合格"] = $count["合格"]?$count["合格"]:0;
                //计算优良率，合格率不合格率
                $form_result[$o]['excellent'] = 0;//优良率
                $form_result[$o]['qualified'] = round($count["合格"]/(0 + $count["合格"]) * 100);//合格率
            }else if(isset($count["优良"]) && !isset($count["合格"]))
            {
                $count_number["excellent_number"] = $count["优良"];
                $count_number["qualified_number"] = 0;
                $form_result[$o]['count'] = $count_number;
                $count["优良"] = $count["优良"]?$count["优良"]:0;
                //计算优良率，合格率不合格率
                $form_result[$o]['excellent'] = round($count["优良"]/($count["优良"] + 0) * 100);//优良率
                $form_result[$o]['qualified'] = 0;//合格率
            }else
            {
                $count_number["excellent_number"] = 0;
                $count_number["qualified_number"] = 0;
                $form_result[$o]['count'] = $count_number;
                //计算优良率，合格率不合格率
                $form_result[$o]['excellent'] = 0;//优良率
                $form_result[$o]['qualified'] = 0;//合格率
            }
        }

        //定义空数组
        $section_form_data = array();

        //柱状图
        $section = Db::name("section")->column("name,id");//标段名
        foreach ($section as $aa=>$bb)
        {
            $section_name[]=$aa;
        }

        foreach($section as $cc =>$dd)
        {

               $temp_data = Db::name('quality_form_info')->alias('r')
                ->join('quality_unit u', 'u.id = r.DivisionId', 'left')
                ->join('quality_division c','c.id=u.division_id','left')
                ->join('section s', 'c.section_id = s.id', 'left')
                ->where('s.id',$dd)
                ->where("r.form_name like '%等级评定表%'")
                ->field("r.form_data,r.id,r.create_time,s.id as section_id")->order("s.id asc")->select();

            if($temp_data)
            {
                $section_form_data[]  = $temp_data;
            }else
            {
                $section_form_data[]  = [];
            }
        }

        foreach($section_form_data as $ee=>$ff)
        {
            foreach ($ff as $gg=>$hh)
            {
                $section_form_data[$ee][$gg]= (unserialize($hh["form_data"]));
            }
        }

        foreach($section_form_data as $ii=>$jj)
        {
            foreach($jj as $kk=>$ll)
            {
                $section_form_data[$ii][$kk] = ($ll[count($ll)-9]);
            }

        }

        foreach($section_form_data as $mm=>$nn)
        {
            foreach($nn as $oo=>$pp)
            {
                if($pp["Step"] != 3)
                {
                    unset($section_form_data[$mm][$oo]);
                }
            }

        }

        //定义一个空的数组
        $form_result_result = array();
        $section_rate_number = array();
        foreach ($section_form_data as $qq=>$rr)
        {
            $count = array_count_values(array_column($rr,"Value"));//统计优良、合格、不合格的数量
            if(isset($count["优良"]) && isset($count["合格"]))
            {
                $section_rate_number["excellent_number"] = $count["优良"];
                $section_rate_number["qualified_number"] = $count["合格"];
                $count["优良"] = $count["优良"]?$count["优良"]:0;
                $count["合格"] = $count["合格"]?$count["合格"]:0;
                $section_rate_number["total"] = $count["优良"] + $count["合格"];

                $form_result_result[$qq]["section_rate_number"] = $section_rate_number;

                //计算优良率，合格率不合格率
                $form_result_result[$qq]['excellent'] = round($count["优良"]/($count["优良"] + $count["合格"]) * 100);//优良率
                $form_result_result[$qq]['qualified'] = round($count["合格"]/($count["优良"] + $count["合格"]) * 100);//合格率
                $form_result_result[$qq]['total_rate'] = $form_result_result[$qq]['excellent'] + $form_result_result[$qq]['qualified'];

            }else if(!isset($count["优良"]) && isset($count["合格"]))
            {
                $section_rate_number["excellent_number"] = 0;
                $section_rate_number["qualified_number"] = $count["合格"];
                $count["合格"] = $count["合格"]?$count["合格"]:0;
                $section_rate_number["total"] = 0 + $count["合格"];

                $form_result_result[$qq]["section_rate_number"] = $section_rate_number;

                //计算优良率，合格率不合格率
                $form_result_result[$qq]['excellent'] = 0;//优良率
                $form_result_result[$qq]['qualified'] = round($count["合格"]/(0 + $count["合格"]) * 100);//合格率
                $form_result_result[$qq]['total_rate'] = $form_result_result[$qq]['excellent'] + $form_result_result[$qq]['qualified'];
            }else if(isset($count["优良"]) && !isset($count["合格"]))
            {
                $section_rate_number["excellent_number"] = $count["优良"];
                $section_rate_number["qualified_number"] = 0;
                $count["优良"] = $count["优良"]?$count["优良"]:0;
                $section_rate_number["total"] = $count["优良"] + 0;

                $form_result_result[$qq]["section_rate_number"] = $section_rate_number;

                //计算优良率，合格率不合格率
                $form_result_result[$qq]['excellent'] = round($count["优良"]/($count["优良"] + 0) * 100);//优良率
                $form_result_result[$qq]['qualified'] = 0;//合格率
                $form_result_result[$qq]['total_rate'] = $form_result_result[$qq]['excellent'] + $form_result_result[$qq]['qualified'];
            }else
            {
                $section_rate_number["excellent_number"] = 0;
                $section_rate_number["qualified_number"] = 0;
                $section_rate_number["total"] = 0;

                $form_result_result[$qq]["section_rate_number"] = $section_rate_number;

                //计算优良率，合格率不合格率
                $form_result_result[$qq]['excellent'] = 0;//优良率
                $form_result_result[$qq]['qualified'] = 0;//合格率
                $form_result_result[$qq]['total_rate'] = $form_result_result[$qq]['excellent'] + $form_result_result[$qq]['qualified'];
            }
        }
        $result = ["section"=>$section_name,"form_result_result"=>$form_result_result];
        $data = ["timeline"=>$timeline,"form_result"=>$form_result];
        return json(["code"=>1,"data"=>$data,"result"=>$result]);
    }
}