<?php

namespace Kernel\Response;
use Kernel\Model;


class Response
{
    public static function sendObject(Model\IModel $model) : void {
        self::_send($model);
    }

    public static function sendArray(array $array) : void {
        self::_send($array);
    }

    public static function sendBuffer(string $buffer) : void{
        echo $buffer;
        exit;
    }

    private static function _send($data){
        if(is_object($data))
            $data = get_object_vars($data);
        
        echo json_encode($data);
        exit;
    }
}