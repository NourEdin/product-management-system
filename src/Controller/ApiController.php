<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /**
     * Constructs the response object in a consistent way
     */
    protected function responseOb($data, $code = 200, $error = ""): JsonResponse {
        return $this->json([
            "data" => $data,
            "error" => $error
        ], $code);
    }

    /**
     * Simplifies how errors are returned from controller functions
     * @param array $data Optional contextual data if provided 
     */
    protected function error($message, $code = 400, $data = []) {
        return $this->responseOb($data, $code, $message);
    }

}