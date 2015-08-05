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
use Think\Storage;
/**
 * 后台上传控制器
 * @author jry <598821125@qq.com>
 */
class UploadController extends AdminController{
    /**
     * 上传列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|path'] = array($condition, $condition,'_multi'=>true);

        //获取所有上传
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('Upload')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Common\Util\Page(D('Upload')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('上传列表') //设置页面标题
                ->addTopButton('resume') //添加启用按钮
                ->addTopButton('forbid') //添加禁用按钮
                ->addTopButton('delete') //添加删除按钮
                ->setSearch('请输入ID/上传关键字', U('index'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('path', '路径')
                ->addTableColumn('size', '大小')
                ->addTableColumn('ctime', '创建时间', 'time')
                ->addTableColumn('sort', '排序')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list) //数据列表
                ->setTableDataPage($page->show()) //数据列表分页
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids    = I('request.ids');
        $status = I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        switch($status){
            case 'delete' : //删除条目
                if(!is_array($ids)){
                    $id_list[0] = $ids;
                }else{
                    $id_list = $ids;
                }
                foreach($id_list as $id){
                    $upload_info = D('Upload')->find($id);
                    if($upload_info){
                        $realpath = realpath('.'.$upload_info['path']);
                        if($realpath){
                            array_map("unlink", glob($realpath));
                            if(count(glob($realpath))){
                                $this->error('删除失败！');
                            }else{
                                $resut = D('Upload')->delete($id);
                                $this->success('删除成功！');
                            }
                        }else{
                            $resut = D('Upload')->delete($id);
                            $this->success('删除成功！');
                        }
                    }
                }
                break;
            default :
                parent::setStatus($model);
                break;
        }
    }
}
