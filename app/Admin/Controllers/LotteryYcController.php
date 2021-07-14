<?php


namespace App\Admin\Controllers;

use App\Http\Model\Kind;
use App\Http\Model\News;
use App\Http\Model\Yc;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Database\Eloquent\Model;


class LotteryYcController extends AdminController
{

    protected function title()
    {
        return '预测结果';
    }

    /**
     * 列表
     * @return Grid
     */
    protected function grid()
    {


//        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//  `kid` int(11) NOT NULL,
//  `qi_start` varchar(40) NOT NULL COMMENT '起始期数',
//  `qi_end` varchar(40) NOT NULL COMMENT '结尾期数',
//  `value` varchar(255) NOT NULL COMMENT '预测内容',
//  `bonus` varchar(255) DEFAULT NULL COMMENT '中奖内容',
//  `type` int(4) NOT NULL COMMENT '预测类型',
//  `state` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1 待开奖 2中奖 3未中将',
//  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        $grid = new Grid(new Yc());
        $grid->model()->orderBy('id', 'desc');
        $grid->model()->orderBy('kid', 'desc');
        $grid->model()->orderBy('qi_start', 'desc');
        $grid->model()->orderBy('type', 'asc');
        $grid->column('id', 'ID')->sortable();
        $grid->column('cole.name', '预测名称名称');
        $grid->column('kind.name', '预测彩种');
        $grid->column('qi_start', '起始期数');
        $grid->column('qi_end', '结束期数');
        //设置颜色，默认`success`,可选`danger`、`warning`、`info`、`primary`、`default`、`success`
        $grid->column('value', '预测内容')->label('danger');
        $grid->column('bonus', '中奖内容')->label('primary');
        $grid->column('state', '中奖状态')->label('warning');
        $grid->column('time', "创建时间");
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            #去掉显示按钮
            $actions->disableView();
        });
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('state', '开奖状态')->radio([
                '1'=>'待开奖',
                '2'=>'中奖',
                '3'=>'未中奖'
            ]);

            $lot=(new Kind())->pluck('name','id');
            // 在这里添加字段过滤器
            $filter->equal('kid', '彩种')->radio($lot);
        });
        return $grid;
    }

}
