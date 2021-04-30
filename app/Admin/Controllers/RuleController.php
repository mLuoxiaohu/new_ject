<?php


namespace App\Admin\Controllers;

use App\Http\Model\Cole;
use App\Http\Model\Kind;
use App\Http\Model\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Http\Model\Record;
use Illuminate\Database\Eloquent\Model;

class RuleController extends AdminController{



    protected function title()
    {
        return '预测规则管理';
    }


    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new Cole());

        $grid->column('id', 'ID')->sortable();
        $grid->column('kind.name','彩种名称');
        $grid->column('name','预测名称')->editable('text');
        $grid->column('value','预测规则')->editable('text');
        $grid->column('reference','规则内容')->limit(30)->ucfirst();
        $grid->column('position','预测位置')->editable('text');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
           $list= Kind::pluck('name','id');
            // 在这里添加字段过滤器
            $filter->like('kid', '彩种')->radio($list);
        });
        $grid->disableActions();
        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Cole);
        $form->text('name','预测名称');
        $form->text('value','预测规则');
        $form->text('positions','预测位置');
        return $form;
    }
}
