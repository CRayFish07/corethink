<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Model;
use Think\Model;
/**
 * 菜单模型
 * @author jry <598821125@qq.com>
 */
class SystemMenuModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title','require','菜单标题必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '菜单标题长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('url','require','链接地址必须填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 根据id获取当前菜单的顶级菜单
     * @author jry <598821125@qq.com>
     */
    public function getRootMenuById($id){
        $map['id'] = array('eq', $id);
        $map['status'] = array('eq', 1);
        $main_menu = array();
        do{
            $main_menu = $this->where($map)->find();
            $map['id'] = array('eq', $main_menu['pid']);
        }while($main_menu['pid'] > 0);
        return $main_menu;
    }

    /**
     * 根据Controller和Action获取当前菜单
     * @author jry <598821125@qq.com>
     */
    public function getMenuByControllerAndAction(){
        $map['status'] = array('eq', 1);
        $map['url'] = array('like', CONTROLLER_NAME.'/'.ACTION_NAME.'%');
        $result = $this->where($map)->order('pid desc')->find();
        return $result;
    }

    /**
     * 根据菜单ID的获取其所有父级菜单
     * @param int $cid 菜单id
     * @return array 父级菜单集合
     * @author jry <598821125@qq.com>
     */
    public function getParentMenu($id){
        if(empty($id)){
            return false;
        }
        $map['status'] = array('eq', 1);
        $menus = $this->where($map)->select();
        $child = $this->field('id,pid,title,url')->find($id); //获取信息
        $pid   = $child['pid'];
        $temp  = array();
        $res[] = $child;
        while(true){
            foreach ($menus as $key => $val){
                if($val['id'] == $pid){
                    $pid = $val['pid'];
                    array_unshift($res, $val); //将父菜单插入到数组第一个元素前
                }
            }
            if($pid == 0){
                break;
            }
        }
        return $res;
    }
}
