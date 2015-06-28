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
use \Org\Util\Date;
/**
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class UserController extends HomeController{
    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function lists(){
        $usertype = I('get.usertype');
        if($usertype){
            $map['usertype'] = $usertype;
        }
        $user_list = D('User')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                              ->where($map)->order('id desc')->select();
        $page = new \Common\Util\Page(D('User')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());
        $this->assign('meta_title', '会员');
        $this->assign('user_list', $user_list);
        $this->display();
    }

    /**
     * 用户个人主页
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $uid = I('get.uid');
        if(!$uid){
            $uid  = is_login();
        }
        $userinfo = D('User')->find($uid);
        $date = new Date((int)$userinfo['birthday']);
        $userinfo['gz'] = $date->magicInfo('GZ');
        $userinfo['xz'] = $date->magicInfo('XZ');
        $userinfo['sx'] = $date->magicInfo('SX');
        $this->assign('meta_title', $userinfo['username'].'的主页');
        $this->assign('info', $userinfo);
        $this->display();
    }

    /**
     * 用户个人中心
     * @author jry <598821125@qq.com>
     */
    public function center(){
        $this->assign('meta_title', '个人中心');
        $this->display();
    }

    /**
     * 用户修改信息
     * @author jry <598821125@qq.com>
     */
    public function profile(){
        if(IS_POST){
            $user_object = D('User');
            $_POST['id'] = $this->is_login();
            $result = $user_object->update($_POST);
            if($result){
                $this->success('信息修改成功');
            }else{
                $this->error($user_object->getError());
            }
        }else{
            $userinfo = D('User')->find($this->is_login());
            $date = new Date((int)$userinfo['birthday']);
            $userinfo['gz'] = $date->magicInfo('GZ');
            $userinfo['xz'] = $date->magicInfo('XZ');
            $userinfo['sx'] = $date->magicInfo('SX');

            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->title('修改'.$userinfo['username'].'的信息')  //设置页面标题
                    ->setUrl(U('')) //设置表单提交地址
                    ->addItem('username', 'text', '用户名', '')
                    ->addItem('avatar', 'picture', '头像', '')
                    ->addItem('sex', 'radio', '性别', '', C('USER_SEX_LIST'))
                    ->addItem('age', 'num', '年龄', '')
                    ->addItem('birthday', 'date', '生日', '自动计算：'.$userinfo['gz'].' '.$userinfo['xz'].' '.$userinfo['sx'])
                    ->addItem('summary', 'text', '签名', '一句话介绍')
                    ->setFormData($userinfo)
                    ->setTemplate('Builder/formbuilder_user')
                    ->display();
        }
    }

    /**
     * 登陆
     * @author jry <598821125@qq.com>
     */
    public function login(){
        if(IS_POST){
            $username = I('username');
            $password = I('password');
            if(!$username){
                $this->error('请输入账号！');
            }
            if(!$password){
                $this->error('请输入密码！');
            }
            $user_object = D('User');
            $uid = $user_object->login($username, $password);
            if(0 < $uid){
                $this->success('登录成功！', Cookie('__forward__') ? : C('HOME_PAGE'));
            }else{
                $this->error($user_object->getError());
            }
        }else{
            if(is_login()){
                $this->error("您已登陆系统", Cookie('__forward__') ? : C('HOME_PAGE'));
            }
            $this->meta_title = '用户登录';
            $this->display();
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        $this->success('退出成功！', Cookie('__forward__') ? : C('HOME_PAGE'));
    }

    /**
     * 用户注册
     * @author jry <598821125@qq.com>
     */
    public function register(){
        if(IS_POST){
            if(!C('TOGGLE_USER_REGISTER')){
                $this->error('注册已关闭！');
            }
            $reg_type = I('post.reg_type');
            switch($reg_type){
                case '1': //邮箱注册
                    $username = I('post.email');
                    $_POST['username'] = 'U'.NOW_TIME;
                    break;
                case '2': //手机号注册
                    $username = I('post.mobile');
                    $_POST['username'] = 'U'.NOW_TIME;
                    break;
            }
            //验证码严格加盐加密验证
            if(user_md5(I('post.verify'), $username) !== session('reg_verify')){
                $this->error('验证码错误！');
            }
            $password = I('post.password');
            $user_object = D('User');
            $data = $user_object->create();
            if($data){
                $id = $user_object->add();
                if($id){
                    session('reg_verify', null);
                    $uid = $user_object->login($username, $password);
                    $this->success('注册成功', U('User/profile'));
                }else{
                    $this->error('注册失败');
                }
            }else{
                $this->error($user_object->getError());
            }
        }else{
            if(is_login()){
                $this->error("您已登陆系统", Cookie('__forward__') ? : C('HOME_PAGE'));
            }
            $this->meta_title = '用户注册';
            $this->display();
        }
    }

    /**
     * 密码重置
     * @author jry <598821125@qq.com>
     */
    public function resetPassword(){
        if(IS_POST){
            $reg_type = I('post.reg_type');
            switch($reg_type){
                case 'email':
                    $username = I('post.email');
                    $condition['email'] = I('post.email');
                    break;
                case 'mobile':
                    $username = I('post.mobile');
                    $condition['mobile'] = I('post.mobile');
                    break;
            }
            //验证码严格加盐加密验证
            if(user_md5(I('post.verify'), $username) !== session('reg_verify')){
                $this->error('验证码错误！');
            }
            $user_object = D('User');
            $data = $user_object->create($_POST, 5); //调用自动验证
            if(!$data){
                $this->error($user_object->getError());
            }
            $result = $user_object->where($condition)->setField('password', $data['password']); //重置密码
            $uid = $user_object->login($username, I('post.password')); //自动登录
            if($uid){
                $this->success('密码重置成功', C('HOME_PAGE'));
            }else{
                $this->error('密码重置失败');
            }
        }else{
            $this->meta_title = '密码重置';
            $this->display();
        }
    }

    /**
     * 图片验证码生成，用于登录和注册
     * @author jry <598821125@qq.com>
     */
    public function verify($vid = 1){
        $verify = new \Think\Verify();
        $verify->entry($vid);
    }

    /**
     * 邮箱验证码，用于注册
     * @author jry <598821125@qq.com>
     */
    public function sendMailVerify(){
        $receiver = I('post.email');
        $title = I('post.title');
        $user_object = D('User');
        $result = $user_object->create($_POST, 5); //调用自动验证
        if(!$result){
            $this->error($user_object->getError());
        }
        $reg_verify = randString(); //生成验证码
        session('reg_verify', user_md5($reg_verify, $receiver));
        $body = '少侠/女侠好：<br>听闻您正使用该邮箱【注册/修改密码】，请在验证码输入框中输入：
        <span style="color:red;font-weight:bold;">'.$reg_verify.'</span>，以完成操作。<br>
        注意：此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改
        密码以保证帐户安全 （工作人员不会向您索取此验证码，请勿泄漏！)';
        if(send_mail($receiver, $title, $body)){
            $this->success('发送成功，请登陆邮箱查收！');
        }else{
            $this->error('发送失败！');
        }
    }

    /**
     * 短信验证码，用于注册
     * @author jry <598821125@qq.com>
     */
    public function sendMobileVerify(){
        $receiver = I('post.mobile');
        $user_object = D('User');
        $result = $user_object->create($_POST, 5); //调用自动验证
        if(!$result){
            $this->error($user_object->getError());
        }
        $reg_verify = randString(); //生成验证码
        session('reg_verify', user_md5($reg_verify, $receiver));
        $body = $title.'验证码：'.$reg_verify;
        if(send_mobile_message($receiver, $title, $body)){
            $this->success('发送成功，请查收！');
        }else{
            $this->error('发送失败！');
        }
    }
}
