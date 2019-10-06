<?php

namespace App\Util;

use Illuminate\Support\Facades\Response;

class AjaxResponse
{

    public $success;
    public $error_code;
    public $error_msg;
    public $data;
    public $msg_display = array();

    public function __construct($success = true, $error_code = 0, $error_msg = '', $data = [], $msg_display = [])
    {
        $this->success = $success;
        $this->error_code = $error_code;
        $this->error_msg = $error_msg;
        $this->data = $data;
        $this->msg_display = $msg_display;
    }

    public function toArray()
    {
        return [
            'success' => $this->success,
            'error' => [
                'code' => $this->error_code,
                'msg' => $this->error_msg,
                'msg_display' => $this->msg_display
            ],
            'data' => $this->data
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_FORCE_OBJECT);
    }

    public function jsonResponse()
    {
        return Response::json($this->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    }

}
