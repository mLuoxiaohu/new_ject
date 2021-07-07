<?php


namespace App\Admin\Controllers;
use App\Http\Model\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Http\Model\Kind;
class LotteryController extends AdminController
{
    protected function title()
    {
        return '彩种管理';
    }
    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new Kind());

        $grid->column('id', 'ID')->sortable();
        $grid->column('name','彩种名称')->editable('text');
        $grid->column('icon','图标')->display(function ($avatar){
            return "<img src='{$avatar}' width='80' height='80'>";
        });
        $states = [
            'on'  => ['value' => '0', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '1', 'text' => '禁止', 'color' => 'default'],
        ];
        $grid->column('none', '状态')->switch($states);
        $grid->column('domain','采集域名');
        $video = [
            'on'  => ['value' => '0', 'text' => '无', 'color' => 'primary'],
            'off' => ['value' => '1', 'text' => '有', 'color' => 'default'],
        ];
        $grid->column('info', '简介')->limit(30)->ucfirst();
        $grid->column('video','是否有视频')->switch($video);
        $grid->column('date','开奖时间');
        $grid->column('time','执行间隔');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('none', '状态')->radio([
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
        $form = new Form(new User);
        $form->text('name','用户昵称');
        $form->text('mobile','用户手机号');
        $states = [
            'on'  => ['value' => '0', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '1', 'text' => '禁止', 'color' => 'default'],
        ];
        $form->switch('none', '状态')->states($states);
        $video = [
            'on'  => ['value' => '0', 'text' => '无', 'color' => 'primary'],
            'off' => ['value' => '1', 'text' => '有', 'color' => 'default'],
        ];
        $form->switch('video', '是否开启直播')->states($video);
        $form->image('icon','图片')->removable()->uniqueName();
        $form->text('domain','采集域名');
        return $form;
    }

}
