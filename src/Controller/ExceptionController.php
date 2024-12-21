<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ExceptionController extends AbstractController
{
    public function handleException(\Throwable $exception): Response
    {

        if ($exception instanceof NotFoundHttpException){
            return $this->json(
                ['message' => "Ressource not found",], 400
            );
        }

        return $this->json(
            ['message' => $exception->getMessage(),], 400
        );

    }
}