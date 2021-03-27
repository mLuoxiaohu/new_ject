<?php
namespace App\Http\Controllers;

/**
 * 状态码
 * Class CodeStatus
 * @package App\Http\Controllers\Api
 */
class CodeStatus{

  const ERROR_IS_NOT_EXISTS =6000;
  const SUCCESS_CODE        =200;  //成功
  const FAIL_CODE           =400;  //失败
  const UNKNOWN_CODE        =404;  //未知
}
