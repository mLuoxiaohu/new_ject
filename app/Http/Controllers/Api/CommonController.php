<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Faker\Generator as AutoData;
/**
 * 公共函数表
 * Class CommonController
 * @package App\Http\Controllers\Api
 */
class CommonController extends BaseController
{
    private $grade;
    private $path_url; #获取根网址
    private $auto_data; #数据填充模型

    public function __construct(Grade $grade, URL $url,AutoData $auto_data)
    {
        $this->grade = $grade;
        $this->path_url = $url::previous();
        $this->auto_data=$auto_data;
    }

    /**
     * 获取等级列表
     * @route /get_grade
     * @method get
     * @param type {可选} 默认 1支付宝等级 2代理等级
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGrade(Request $request)
    {
        try {
            $type = $request->get('type', 1);
            $result = $this->grade->where('type', $type)->orderBy('grade','asc')->get();
            if ($result) {
                foreach ($result as $k => &$v) {
                    if($this->isUrlHeader($v->icon) ===  false){
                        $v->icon = $this->path_url . $v->icon;
                    }
                }
            }
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }






    /**
     * 修改等级列表
     * @route /update_grade/{等级id}
     * @method put
     * @param name 名称 {可选}
     * @param icon 图标 {可选}
     * @param grade 等级 {可选}
     * @param type 类型 {可选} 1支付宝等级 2代理等级
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGrade(Request $request, $id)
    {
        try {
            $param = [
                'icon' => 'mimes:jpeg,bmp,png,jpg',
            ];
            $message = [
                'icon.mimes' => '只支持图片:jpeg,bmp,png,jpg 格式!'
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $obj = $this->grade->where('id', $id)->first();
            if (!$obj) return $this->_error(self::DATA_NULL);
            if (count($input) <= 0) return $this->_error(self::NOT_CHANGE_CONTENT);
            if (isset($input['icon'])) {
                if (!empty($obj->icon)) $this->deleteFile($obj->icon);
                $fileImg = $this->OneUploadFile($input['icon'], 'image');
                if ($fileImg['code'] != 200) return $this->_error($fileImg['msg']);
                $input['icon'] = $fileImg['name'] ?? '/default.png';
            }
            $result = $obj->update($input);
            if (!$result) return $this->_error(self::UPDATE_FAIL);
            return $this->_success([], self::UPDATE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /***
     * 添加图标
     * @route /add_grade
     * @method post
     * @param name 名称
     * @param icon 图标
     * @param type 类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGrade(Request $request)
    {
        try {
            $param = [
                'name' => 'required',
                'icon' => 'required|mimes:jpeg,bmp,png,jpg,gif',
                'type' => 'required',
                'grade' => 'required'
            ];
            $message = [
                "name.required" => "名称不能为空",
                'type.required' => '类型不能为空',
                'icon.required' => '图标不能为空',
                'icon.mimes' => '只支持图片:jpeg,bmp,png,jpg 格式!',
                'grade.required' => '等级不能为空'
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $fileImg = $this->OneUploadFile($input['icon'], 'image');
            if ($fileImg['code'] != 200) return $this->_error($fileImg['msg']);
            $input['icon'] = $fileImg['name'] ?? '/default.png';
            $result = $this->grade->create($input);
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * 删除图标
     * @method Delete
     * @route /delete_grade/{等级id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGrade($id)
    {
        try {
            $result = $this->grade->where('id', $id)->first();
            if (!$result) return $this->_error(self::DELETE_DATA_NOT_NULL);
            $isOk = $result->delete();
            if (!$isOk) return $this->_error(self::DELETE_FAILED);
            return $this->_success([], self::DELETE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

}
