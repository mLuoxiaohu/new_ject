<?php


namespace App\Admin\Controllers;
use App\Http\Model\News;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\AdminController;


class NewsListController extends AdminController
{

    protected function title()
    {
        return '新闻列表';
    }
    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new News());
        $grid->model()->orderBy('id','desc');
        $grid->column('id','ID')->sortable();
        $grid->column('newclass.name','分类名称');
        $grid->column('title','标题');
        $grid->column('icon', '图片')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        $grid->column('content', '内容')->limit(30);
        $grid->column('time',"创建时间");
        $grid->actions(function (Grid\Displayers\Actions $actions){
            #去掉显示按钮
            $actions->disableView();
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('kid', '彩种')->radio([
                '展示'=>'1',
                '隐藏'=>'2'
            ]);
        });
        return $grid;
    }

}
