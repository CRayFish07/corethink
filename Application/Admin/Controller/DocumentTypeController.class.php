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
 * 后台类型控制器
 * @author jry <598821125@qq.com>
 */
class DocumentTypeController extends AdminController{
    /**
     * 类型列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title|name'] = array($condition, $condition, $condition,'_multi'=>true);

        //获取所有类型
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('DocumentType')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort asc,id asc')->select();
        $page = new \Common\Util\Page(D('DocumentType')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('类型列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/类型标题', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('icon', '图标', 'icon')
                ->addField('name', '名称', 'text')
                ->addField('title', '标题', 'text')
                ->addField('ctime', '创建时间', 'time')
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
     * 新增类型
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $document_type_object = D('DocumentType');
            $data = $document_type_object->create();
            if($data){
                $id = $document_type_object->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($document_type_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增类型')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('name', 'text', '类型名称', '类型名称')
                    ->addItem('title', 'text', '类型标题', '类型标题')
                    ->addItem('icon', 'icon', '图标', '类型图标')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->display();
        }
    }

    /**
     * 编辑类型
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $document_type_object = D('DocumentType');
            $data = $document_type_object->create();
            if($data){
                if($document_type_object->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($document_type_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑类型')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('id', 'hidden', 'ID', 'ID')
                    ->addItem('name', 'text', '类型名称', '类型名称')
                    ->addItem('title', 'text', '类型标题', '类型标题')
                    ->addItem('field_group', 'textarea', '字段分组', '字段分组')
                    ->addItem('icon', 'icon', '图标', '类型图标')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->setFormData(D('DocumentType')->find($id))
                    ->display();
        }
    }
}
