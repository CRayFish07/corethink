<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
const THINK_ADDON_PATH = './Addons/';
return array(
    //数据库配置
    'DB_TYPE'   => $_SERVER[ENV_PRE.'DB_TYPE'] ? : '[DB_TYPE]', // 数据库类型
    'DB_HOST'   => $_SERVER[ENV_PRE.'DB_HOST'] ? : '[DB_HOST]', // 服务器地址
    'DB_NAME'   => $_SERVER[ENV_PRE.'DB_NAME'] ? : '[DB_NAME]', // 数据库名
    'DB_USER'   => $_SERVER[ENV_PRE.'DB_USER'] ? : '[DB_USER]', // 用户名
    'DB_PWD'    => $_SERVER[ENV_PRE.'DB_PWD']  ? : '[DB_PWD]', // 密码
    'DB_PORT'   => $_SERVER[ENV_PRE.'DB_PORT'] ? : '[DB_PORT]', // 端口
    'DB_PREFIX' => $_SERVER[ENV_PRE.'DB_PREFIX'] ? : '[DB_PREFIX]', // 数据库表前缀

    //URL模式
    'URL_MODEL' => 2,

    //CoreThink当前版本
    'CORETHINK_VERSION' => '1.0Beta',

    //后台分页每页行数数
    'ADMIN_PAGE_ROWS' => 10,

    //全局过滤配置
    'DEFAULT_FILTER' => '', //默认为htmlspecialchars

    //应用配置
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common'),
    'MODULE_ALLOW_LIST'  => array('Home','Admin','Install'),
    'AUTOLOAD_NAMESPACE' => array('Addons' => THINK_ADDON_PATH), //扩展模块列表

    //URL配置
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写

    //表单类型
    'FORM_ITEM_TYPE' => array(
        'hidden'     => '隐藏',
        'num'        => '数字',
        'text'       => '字符串',
        'textarea'   => '文本',
        'array'      => '数组',
        'password'   => '密码',
        'radio'      => '单选按钮',
        'checkbox'   => '复选框',
        'select'     => '下拉框',
        'date  '     => '日期',
        'datetime'   => '时间',
        'picture'    => '图片',
        'kindeditor' => '编辑器',
        'tag  '      => '标签',
    ),

    //文件上传相关配置
    'UPLOAD_CONFIG' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),
);
