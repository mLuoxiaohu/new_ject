<?php

namespace App\Admin\Controllers;

use App\Admin\Model\MessageModel;
use App\Http\Model\Opinion;
use App\Http\Model\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class LeaveMsgController extends AdminController
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
        $grid->model()->where('pid', 0);
        $grid->model()->orderBy('id', 'desc');
        $grid->column('id', 'ID')->sortable();
        $grid->column('nickname','用户昵称')->expand(function ($model) {

            $comments = $model->children()->take(10)->get()->map(function ($comment) {
                return $comment->only(['id','nickname', 'content','create_time']);
            });
            return new Table(['ID','昵称', '内容', '发布时间'], $comments->toArray());
        });

        $grid->column('content','评论内容')->editable('textarea');
        $grid->column('avatar','头像')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => '2', 'text' => '通过', 'color' => 'primary'],
            'off' => ['value' => '3', 'text' => '不通过', 'color' => 'default'],
        ];
        $grid->column('state', '审核状态')->switch($states);
        $grid->column('create_time',trans('admin.created_at'));
        $grid->column('update_time',trans('admin.updated_at'));
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            // 添加操作按钮
            $actions->add(new MessageModel());
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('state', '状态')->radio([
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
        $states = [
            'on'  => ['value' => '2', 'text' => '通过', 'color' => 'primary'],
            'off' => ['value' => '3', 'text' => '不通过', 'color' => 'default'],
        ];
        $form->switch('state','状态')->states($states);
        $form->textarea('content','评论内容');
        $form->image('avatar','头像')->removable()->uniqueName();
        return $form;
    }
}
