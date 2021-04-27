<?php


namespace App\Admin\Controllers;
use App\Http\Model\Kind;
use App\Http\Model\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Http\Model\Record;
class OpenLottery extends AdminController
{


    protected function title()
    {
        return '开奖管理';
    }
    /**
     * 列表
     * @return Grid
     */
    protected function grid(){



        $grid = new Grid(new Record());

        $grid->column('id', 'ID')->sortable();
        $grid->column('kind.name','彩种名称');
        $grid->column('kind.icon','图标')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        $grid->column('periods','开奖期号')->editable('text');
        $grid->column('number','开奖号码')->editable('text');
        $grid->column('kind.info','开奖信息')->limit(30);
        $grid->column('time','开奖时间')->display(function ($time){
           return date('Y-m-d H:i:s',$time);
        });
        $grid->disableActions();

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $lot=(new Kind())->pluck('name','id')->toArray();
//            $new=[''=>'所有'];
//            array_unshift($lot,$new);
            // 在这里添加字段过滤器
            $filter->like('kid', '彩种')->radio($lot);
        });
        return $grid;
    }

    protected function form()
    {
        $form = new Form(new User);
        $form->text('periods','开奖期号');
        $form->text('number','开奖号码');
        return $form;
    }



}
