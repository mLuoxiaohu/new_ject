<?php

namespace App\Admin\Controllers;

use App\Http\Model\Opinion;
use App\Http\Model\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LeaveMsg extends AdminController
{
    protected function title()
    {
        return '用户留言';
    }


    /**
     * 列表
     * @return Grid
     */
    protected function grid(){
        $grid = new Grid(new Opinion());
        $grid->column('id', 'ID')->sortable();
        $grid->column('nickname','用户昵称');
        $grid->column('content','评论内容')->editable('textarea');
        $grid->column('avatar','头像')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => 2, 'text' => '通过', 'color' => 'primary'],
            'off' => ['value' => 3, 'text' => '不通过', 'color' => 'default'],
        ];
//        $grid->column('status')->switch($states);
//        $grid->column('state','状态')->display(function ($state){
//            return Opinion::$states[$state];
//        });
        $grid->column('state', '状态')->switch($states);;
        $grid->column('create_time',trans('admin.created_at'));
        $grid->column('update_time',trans('admin.updated_at'));
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('state', '状态')->radio([
                ''   => '所有',
                1=>'未审核',2=>'审核通过',3=>'未通过'
            ]);;
        });
        return $grid;
    }


    protected function form()
    {
        $form = new Form(new Opinion());
        $form->text('nickname','用户昵称');
        $form->text('mobile','用户手机号');
        $form->select('state', '状态')->options([
            2=>'审核通过',3=>'未通过'
        ]);
        $form->textarea('content','评论内容');
        $form->image('avatar','头像')->removable()->uniqueName();
        return $form;
    }
}
