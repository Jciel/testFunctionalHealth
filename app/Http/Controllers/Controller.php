<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

abstract class Controller
{
    public function error($message, $httpCode = null, $errors = [])
    {
        return response()->json(
            ['message' => $message, 'errors' => $errors],
            $httpCode ?? Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
