<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
/**
 * 后台用户控制器
 * @author jry <598821125@qq.com>
 */
class UserController extends AdminController{
    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index($status = '0,1'){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|email|mobile'] = array($condition, $condition, $condition, $condition,'_multi'=>true);

        //获取所有用户
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('User')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Think\Page(D('User')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('评论列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/用户名/邮箱/手机号', U('index'))
                ->addField('id', 'UID', 'text')
                ->addField('username', '用户名', 'text')
                ->addField('email', '邮箱', 'text')
                ->addField('mobile', '手机号', 'text')
                ->addField('score', '积分', 'text')
                ->addField('money', '余额', 'text')
                ->addField('last_login_time', '最后登录时间时间', 'time')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($page->show())
                ->display();
    }

    /**
     * 新增用户
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $user = D('User');
            $data = $user->create();
            if($data){
                $id = $user->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($user->getError());
            }
        }else{
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'游荡中')), $all_group);
            $this->assign('all_group', $all_group);
            $this->meta_title = '新增用户';
            $this->display('edit');
        }
    }

    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $user = D('User');
            //不修改密码时销毁变量
            if($_POST['password'] == ''){
                unset($_POST['password']);
            }else{
                $_POST['password'] = user_md5($_POST['password']);
            }
            //不允许更改超级管理员用户组
            if($_POST['id'] == 1){
                unset($_POST['group']);
            }
            if($_POST['extend']){
                $_POST['extend'] = json_encode($_POST['extend']);
            }
            if($user->save($_POST)){
                $user->updateUserCache($_POST['id']);
                $this->success('更新成功', U('index'));
            }else{
                $this->error('更新失败', $user->getError());
            }
        }else{
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'游荡中')), $all_group);
            $this->assign('all_group', $all_group);
            $this->assign('info', D('User')->getUserById($id));
            $this->meta_title = '编辑用户';
            $this->display();
        }
    }
}
