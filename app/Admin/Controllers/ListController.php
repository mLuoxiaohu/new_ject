<?php


namespace App\Admin\Controllers;

use App\Http\Model\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class ListController extends AdminController
{
    private $user;
    public function __construct(User $user)
    {
     $this->user=$user;
    }

    protected function title()
    {
        return '用户列表';
    }

//'nickname','mobile','sex','password','avatar','state','create_time',
//'update_time','signature','login_ip','login_time','coin'
    /**
     * 列表
     * @return Grid
     */
    protected function grid(){

        $grid = new Grid(new User());

        $grid->column('id', 'ID')->sortable();
        $grid->column('nickname','用户昵称');
        $grid->column('mobile','用户手机号');
        $grid->column('sex','用户性别');
        $grid->column('avatar','头像')->display(function ($avatar){
          return "<img src='{$avatar}' width='80' height='80'>";
        });
        $states = [
            'on'  => ['value' => '1', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '2', 'text' => '禁止', 'color' => 'default'],
        ];
        $grid->column('state', '状态')->switch($states);
        $grid->column('login_ip','登录IP');
        $grid->column('login_time','登录时间');
        $grid->column('create_time',trans('admin.created_at'));
        $grid->column('update_time',trans('admin.updated_at'));
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
        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));
        $show->field('id', 'ID');
        $show->field('nickname','用户昵称');
        $show->field('mobile','用户手机号');
        $show->field('sex','用户性别');
        $show->field('avatar','头像');
        $show->field('state','状态');
        $show->field('login_ip','登录IP');
        $show->field('login_time','登录时间');
        $show->field('create_time',trans('admin.created_at'));
        $show->field('update_time',trans('admin.updated_at'));
        return $show;
    }

    protected function form()
    {
        $form = new Form(new User);
        $form->text('nickname','用户昵称');
        $form->text('mobile','用户手机号');
        $form->password('password','用户密码');
        $form->saving(function (Form $form) {
            if ($form->password) {
                $form->password = Hash::make($form->password);
            }else{
                unset($form->password);
            }
        });
        $states = [
            'on'  => ['value' => '1', 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => '2', 'text' => '禁止', 'color' => 'default'],
        ];
        $form->switch('state', '状态')->states($states);
        $form->image('avatar','头像')->removable()->uniqueName();
        return $form;
    }
}
