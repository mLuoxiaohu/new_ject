<?php


namespace App\Admin\Model;

use App\Http\Model\Opinion;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MessageModel extends RowAction
{
    public $name = '回复';

    public function handle(Opinion $opinion, Request $request)
    {
        // $request 获取 下方 form 表单内容

        // 处理错误
        try {
            // $model 获取提交行信息
            $id = $request->post('_key');
            $content = $request->post('content');
            $data = [
                'avatar' => 'default0.png',
                'nickname' => '管理员00'.rand(1,5),
                'content' => $content,
                'pid' => $id,
                'state'=>'2'
            ];
            $re = $opinion->create($data);
            if ($re) return $this->response()->success('回复成功')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('产生错误：' . $e->getMessage());
        }
    }


    // 创建弹出模态框
    public function form()
    {
        $this->text('content', '评论内容')->rules('required');
    }
}

