<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 * @author jry <598821125@qq.com>
 */
class HomeController extends Controller{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        //读取数据库中的配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = D('SystemConfig')->lists();
            S('DB_CONFIG_DATA',$config);
        }

        //模板相关配置
        $config['TMPL_PARSE_STRING']['__PUBLIC__'] = __ROOT__.'/Public';
        $config['TMPL_PARSE_STRING']['__IMG__'] = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/img';
        $config['TMPL_PARSE_STRING']['__CSS__'] = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/css';
        $config['TMPL_PARSE_STRING']['__JS__']  = __ROOT__.'/Application/'.MODULE_NAME.'/View/'.$config['DEFAULT_THEME'].'/Public/js';
        $config['HOME_PAGE'] = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
        C($config); //添加配置

        if(!C('TOGGLE_WEB_SITE')){
            $this->error('站点已经关闭，请稍后访问~');
        }

        $this->assign('meta_keywords', C('WEB_SITE_KEYWORD'));
        $this->assign('meta_description', C('WEB_SITE_DESCRIPTION'));
        $this->assign('__USER__', session('user_auth')); //用户登录信息
    }

    /**
     * 用户登录检测
     * @author jry <598821125@qq.com>
     */
    protected function is_login(){
        //用户登录检测
        $uid = is_login();
        if($uid){
            return $uid;
        }else{
            $data['login'] = 1;
            $this->error('请先登陆', U('User/login'), $data);
        }
    }

    /**
     * 获取所有数据并转换成一维数组
     * @author jry <598821125@qq.com>
     */
    public function selectListAsTree($model, $map = null, $extra = null){
        //获取列表
        $map['status'] = array('eq', 1);
        $list = D($model)->where($map)->select();

        //转换成树状列表
        $tree = new \Common\Util\Tree();
        $list = $tree->toFormatTree($list);

        if($extra){
            $result[0] = $extra;
        }

        //转换成一维数组
        foreach($list as $val){
            $result[$val['id']] = $val['title_show'];
        }
        return $result;
    }
}
