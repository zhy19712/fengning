<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/3
 * Time: 9:45
 */


function addlog($operation_id='')
{
    //获取网站配置
    $web_config = \think\Db::name('webconfig')->where('web', 'web')->find();
    if ($web_config['is_log'] == 1) {
        $data['operation_id'] = $operation_id;
        $data['admin_id'] = \think\Session::get('admin');//管理员id
        $request = \think\Request::instance();
        $data['ip'] = $request->ip();//操作ip
        $data['create_time'] = time();//操作时间
        $url['module'] = $request->module();
        $url['controller'] = $request->controller();
        $url['function'] = $request->action();
        //获取url参数
        $parameter = $request->path() ? $request->path() : null;
        //将字符串转化为数组
        $parameter = explode('/', $parameter);
        //剔除url中的模块、控制器和方法
        foreach ($parameter as $key => $value) {
            if ($value != $url['module'] and $value != $url['controller'] and $value != $url['function']) {
                $param[] = $value;
            }
        }

        if (isset($param) and !empty($param)) {
            //确定有参数
            $string = '';
            foreach ($param as $key => $value) {
                //奇数为参数的参数名，偶数为参数的值
                if ($key % 2 !== 0) {
                    //过滤只有一个参数和最后一个参数的情况
                    if (count($param) > 2 and $key < count($param) - 1) {
                        $string .= $value . '&';
                    } else {
                        $string .= $value;
                    }
                } else {
                    $string .= $value . '=';
                }
            }
        } else {
            //ajax请求方式，传递的参数path()接收不到，所以只能param()
            $string = [];
            $param = $request->param();
            foreach ($param as $key => $value) {
                if (!is_array($value)) {
                    //这里过滤掉值为数组的参数
                    $string[] = $key . '=' . $value;
                }
            }
            $string = implode('&', $string);
        }
        $data['admin_menu_id'] = empty(\think\Db::name('admin_menu')->where($url)->where('parameter', $string)->value('id')) ? \think\Db::name('admin_menu')->where($url)->value('id') : \think\Db::name('admin_menu')->where($url)->where('parameter', $string)->value('id');

        //return $data;
        \think\Db::name('admin_log')->insert($data);
    } else {
        //关闭了日志
        return true;
    }
}

/**
 * 数组分页函数  核心函数  array_slice
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
 * $count   每页多少条数据
 * $page   当前第几页
 * $array   查询出来的所有数组
 * order 0 - 不变     1- 反序
 */
function page_array($count,$page,$array,$order){
    global $countpage; #定全局变量
    $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
    $start=($page-1)*$count; #计算每次分页的开始位置
    if($order==1){
        $array=array_reverse($array);
    }
    $totals=count($array);
    $countpage=ceil($totals/$count); #计算总页面数
    $pagedata=array();
    $pagedata=array_slice($array,$start,$count);
    return $pagedata;  #返回查询数据
}