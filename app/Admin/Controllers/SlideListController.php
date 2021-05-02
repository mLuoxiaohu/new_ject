<?php


namespace App\Admin\Controllers;

use App\Http\Model\Slide;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\AdminController;
class SlideListController extends AdminController
{

    protected function title()
    {
        return '轮播图列表';
    }



    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new Slide);
        $grid->model()->orderBy('id','desc');
        $grid->column('id','ID')->sortable();
        $grid->column('cover', '图片地址')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        $grid->column('url', '跳转链接')->editable('text');
        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => '1', 'text' => '展示', 'color' => 'primary'],
            'off' => ['value' => '2', 'text' => '隐藏', 'color' => 'default'],
        ];
        $grid->column('state', '展示状态')->switch($states);
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

    protected function form()
    {
        $form = new Form(new Slide());
        $form->text('url','跳转链接');
        $states = [
            'on'  => ['value' => '1', 'text' => '展示', 'color' => 'primary'],
            'off' => ['value' => '2', 'text' => '隐藏', 'color' => 'default'],
        ];
        $form->switch('state','展示状态')->states($states);
        $form->image('cover','头像')->removable()->uniqueName();
        return $form;
    }
}
