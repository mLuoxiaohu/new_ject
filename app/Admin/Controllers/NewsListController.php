<?php


namespace App\Admin\Controllers;
use App\Http\Model\News;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Database\Eloquent\Model;

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

}
