<?php


namespace App\Admin\Controllers;


use App\Http\Model\Config;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class ConfController  extends AdminController
{

    protected function title(){return '配置管理';}
    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new Config());
        $grid->column('id', 'ID')->sortable();
        $grid->column('name','配置名称')->editable('text');
        $grid->column('key','键名称')->editable('text');
        $states = [
            'on'  => ['value' => '1', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '2', 'text' => '禁止', 'color' => 'default'],
        ];
        $grid->column('state', '状态')->switch($states);
        $grid->column('value','值')->editable('text');;
        $grid->column('create_time','创建时间');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('state', '状态')->radio([
                ''   => '所有',
                1    => '正常',
                2    => '禁用',
            ]);
        });
        $grid->disableActions();
        return $grid;
    }


    protected function form()
    {
        $form = new Form(new Config());
        $form->text('name','配置名称');
        $form->text('key','配置键');
        $states = [
            'on'  => ['value' => '0', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '1', 'text' => '禁止', 'color' => 'default'],
        ];
        $form->switch('state', '状态')->states($states);
        $form->text('value','采集域名');
        return $form;
    }
}
