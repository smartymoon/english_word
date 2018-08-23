<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SUCCESS_CODE = 200;
    const CREATED_CODE = 201;
    const UPDATED_CODE = 202;
    const DELETED_CODE = 203;
    const FAIL_CODE = 205;
    const NOT_FOUND_CODE = 404;

    /**
     * @param $message
     * @param array $other
     * @param int $code
     * @param array $header
     * @param int $options
     * @return array|\Illuminate\Http\JsonResponse
     * @internal param $data
     * @internal param $message
     */
    protected function status($message, $code = 200, $other = [], $header = array(), $options = 0)
    {
        $other = is_array($other)?$other:$other->toArray();
        $data = array_merge([
            'message' => $message,
            'code'    => $code,
        ], ['data'=>$other]);
        return Response::json($data, 200, $header, $options);
    }

    public function success($message = '操作成功', $data = [])
    {
        return $this->status($message, self::SUCCESS_CODE, $data);
    }

    public function fail($message = '操作失败', $data = [])
    {
        return $this->status($message, self::FAIL_CODE, $data);
    }
}
